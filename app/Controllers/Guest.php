<?php

namespace App\Controllers;

class Guest extends BaseController
{
    public function nota($no_nota)
    {
        $no_nota = str_replace("-", '/', $no_nota);

        $data = db('nota')->where('no_nota', $no_nota)->get()->getResultArray();
        if (count($data) == 0) {
            echo '<h2 style="font-family: Arial, sans-serif;text-align:center">Data tidak ada</h2>';
            die;
        }
        $h = (count($data) == 1 ? 0 : count($data) * 4);

        $set = [
            'mode' => 'utf-8',
            'format' => [80, 90 + $h],
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0
        ];

        $mpdf = new \Mpdf\Mpdf($set);
        $mpdf->SetAutoPageBreak(true);

        $judul = "NOTA " . $no_nota;
        // Dapatkan konten HTML
        // $logo = '<img width="90" src="logo.png" alt="KOP"/>';
        $html = view('guest/nota', ['judul' => $judul, 'data' => $data, 'no_nota' => $no_nota]); // view('pdf_template') mengacu pada file view yang akan dirender menjadi PDF

        // Setel konten HTML ke mPDF
        $mpdf->WriteHTML($html);

        // Output PDF ke browser
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output($judul . '.pdf', 'I');
    }
    public function laporan($order = "All", $tahun = 2025, $bulan = 1)
    {
        $rangkuman = [];
        $tables = ['transaksi', 'pengeluaran'];
        $total = ['transaksi' => 0, 'pengeluaran' => 0];
        foreach (bulans() as $b) {
            if ($b['angka'] <= $bulan) {
                $bulanan = [];
                foreach ($tables as $i) {
                    $db = db($i);
                    $db->select('*');
                    $res = $db->orderBy('tgl', 'ASC')
                        ->where("MONTH(FROM_UNIXTIME(tgl))", $b['satuan'])
                        ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
                        ->get()
                        ->getResultArray();
                    $tot = array_sum(array_column($res, 'biaya'));
                    $total[$i] += $tot;
                    $bulanan[] = $tot;
                }

                $rangkuman[] = ['tgl' => $b['bulan'], 'masuk' => $bulanan[0], 'keluar' => $bulanan[1], 'total' => $bulanan[0] - $bulanan[1]];
            }
        }


        $data = [];
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        if ($order == "All") {
            foreach ($tables as $i) {
                $db = db($i);
                $db->select('*');
                $res = $db->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
                    ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
                    ->get()
                    ->getResultArray();
                $tot = array_sum(array_column($res, 'biaya'));
                $data[$i] = ['data' => $res, 'total' => $tot];
            }
        }
        if ($order == "Harian") {
            $total = 0;
            $temp = [];
            for ($x = 1; $x <= $jumlahHari; $x++) {
                $harian = [];
                foreach ($tables as $i) {
                    $db = db($i);
                    $db->select('*');
                    $res = $db->orderBy('tgl', 'ASC')
                        ->where("DAY(FROM_UNIXTIME(tgl))", $x)
                        ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
                        ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
                        ->get()
                        ->getResultArray();
                    $tot = array_sum(array_column($res, 'biaya'));
                    $harian[$i] = $tot;
                }
                $harian['total'] = $harian['transaksi'] - $harian['pengeluaran'];

                $temp[] = $harian;
                $total += $harian['total'];
            }
            $data['bulan_ini'] = ['bulan' => bulans($bulan)['bulan'], 'data' => $temp, 'total' => $total];
        }

        if ($order == "Tahunan") {
            foreach (tahuns('pengeluaran') as $t) {
                $tahunan = [];
                foreach ($tables as $i) {
                    $db = db($i);
                    $db->select('*');
                    $res = $db->orderBy('tgl', 'ASC')
                        ->where("YEAR(FROM_UNIXTIME(tgl))", $t['tahun'])
                        ->get()
                        ->getResultArray();
                    $tot = array_sum(array_column($res, 'biaya'));
                    $total[$i] += $tot;
                    $tahunan[] = $tot;
                }

                $data[] = ['tgl' => $t['tahun'], 'masuk' => $tahunan[0], 'keluar' => $tahunan[1], 'total' => $tahunan[0] - $tahunan[1]];
            }
        }
        // dd($data);
        $set = [
            'mode' => 'utf-8',
            'format' => [210, 330],
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5
        ];

        $judul = "LAPORAN " . strtoupper(($order == "All" ? "detail" : $order)) . " " . strtoupper(profile()['nama']) . " BULAN " . strtoupper(bulans($bulan)['bulan']) . " TAHUN " . $tahun;
        // Dapatkan konten HTML
        $logo = '<img width="90" src="logo.png" alt="KOP"/>';
        $mpdf = new \Mpdf\Mpdf($set);
        $html = view('guest/laporan', ['judul' => $judul, 'data' => $data, 'logo' => $logo, 'bulan' => bulans($bulan)['bulan'], 'order' => $order, 'rangkuman' => $rangkuman]); // view('pdf_template') mengacu pada file view yang akan dirender menjadi PDF

        // Setel konten HTML ke mPDF
        $mpdf->WriteHTML($html);

        // Output PDF ke browser
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output($judul . '.pdf', 'I');
    }

    public function login()
    {
        $username = strtolower(clear($this->request->getVar('username')));
        $password = $this->request->getVar('password');

        $q = db('user')->where('username', $username)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(), "User not found");
        }

        if (!password_verify($password, $q['password'])) {
            gagal(base_url(), "Password salah");
        }

        $data = [
            'id' => $q['id']
        ];

        session()->set($data);
        sukses(base_url('home'), 'Login sukses.');
    }

    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('sukses', "Logout sukses");
        header("Location: " . base_url());
        die;
    }
}
