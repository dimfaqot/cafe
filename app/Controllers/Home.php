<?php

namespace App\Controllers;

class Home extends BaseController
{
    function __construct()
    {
        if (!session('id')) {
            session()->setFlashdata('gagal', "Ligin first");
            header("Location: " . base_url());
            die;
        }
    }
    public function index(): string
    {

        return view(menu()['controller'] . '/' . menu()['controller'] . '_' . 'landing', ['judul' => menu()['menu']]);
    }

    public function delete()
    {
        $id = clear($this->request->getVar('id'));
        $tabel = clear($this->request->getVar('tabel'));
        $q = db($tabel)->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id not found");
        }

        (db($tabel)->where('id', $id)->delete()) ? sukses_js("Sukses") : gagal_js("Gagal");
    }

    public function statistik()
    {
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));
        $order = clear($this->request->getVar('order'));
        $jenis = clear($this->request->getVar('jenis'));

        if ($order == 'laporan') {
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $sub_menu = ['Harian', 'Bulanan', 'Tahunan'];
            $tables = ['transaksi', 'pengeluaran'];
            $total = ['transaksi' => 0, 'pengeluaran' => 0];
            $data = [];
            if ($jenis == "All") {
                foreach ($tables as $i) {
                    $db = db($i);
                    $db->select('*');
                    $res = $db->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
                        ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
                        ->get()
                        ->getResultArray();
                    $tot = array_sum(array_column($res, 'biaya'));
                    $data[$i] = ['total' => $tot];
                }
            }
            if ($jenis == "Harian") {
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
                        $total[$i] += $tot;
                        $harian[] = $tot;
                    }

                    $data[] = ['tgl' => $x, 'masuk' => $harian[0], 'keluar' => $harian[1]];
                }
            }
            if ($jenis == "Bulanan") {
                foreach (bulans() as $b) {
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

                    $data[] = ['tgl' => $b['bulan'], 'masuk' => $bulanan[0], 'keluar' => $bulanan[1]];
                }
            }
            if ($jenis == "Tahunan") {
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

                    $data[] = ['tgl' => $t['tahun'], 'masuk' => $tahunan[0], 'keluar' => $tahunan[1]];
                }
            }
            if ($jenis == "Backup") {
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

                    $data[] = ['tgl' => $t['tahun'], 'masuk' => $tahunan[0], 'keluar' => $tahunan[1]];

                    $q = db('backup')->where('tahun', $t['tahun'])->get()->getRowArray();

                    if (!$q) {
                        $insert = [
                            'tahun' => $t['tahun'],
                            'masuk' => $tahunan[0],
                            'keluar' => $tahunan[1],
                            'saldo' => $tahunan[0] - $tahunan[1],
                            'keep' => 1
                        ];
                        db('backup')->insert($insert);
                    } else {
                        if ($q['keep'] == 0) {
                            $q['masuk'] = $tahunan[0];
                            $q['keluar'] = $tahunan[1];
                            $q['saldo'] = $tahunan[0] - $tahunan[1];
                            $q['keep'] = 1;
                            db('backup')->where('id', $q['id'])->update($q);
                        }
                    }

                    $backup = db('backup')->select('*')->orderBy('tahun', 'ASC')
                        ->get()
                        ->getResultArray();
                    $tot = array_sum(array_column($backup, 'saldo'));

                    sukses_js("Ok", $backup, $tot);
                }
            }
        } else {
            $sub_menu = ['Makanan', 'Minuman', 'Snack'];
            if ($order == 'pengeluaran') {
                $sub_menu[] = 'Kulakan';
                foreach (options("Inv") as $i) {
                    $sub_menu[] = $i['value'];
                }
            }

            $db = db($order);
            $db->select('*');
            if ($jenis !== "All") {
                $db->where('jenis', $jenis);
            }
            $db->orderBy('tgl', 'ASC')
                ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
                ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun);
            if ($order == "hutang") {
                $db->orderBy('nama', 'ASC');
            }
            $data = $db->get()
                ->getResultArray();
            $total = array_sum(array_column($data, 'biaya'));
        }
        sukses_js("Ok", $data, $total, $sub_menu);
    }

    public function unlock()
    {
        $id = clear($this->request->getVar('id'));
        $keep = clear($this->request->getVar('keep'));

        $keep = ($keep == 0 ? 1 : 0);

        $q = db('backup')->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id not found");
        }

        $q['keep'] = $keep;

        if (db('backup')->where('id', $q['id'])->update($q)) {
            $backup = db('backup')->select('*')->orderBy('tahun', 'ASC')
                ->get()
                ->getResultArray();
            $tot = array_sum(array_column($backup, 'saldo'));

            sukses_js("Ok", $backup, $tot);
        } else {
            gagal_js("Unlock gagal");
        }
    }
}
