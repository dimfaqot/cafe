<?php

namespace App\Controllers;

class Home extends BaseController
{

    function __construct()
    {
        helper('functions');
        if (!session('id')) {
            gagal(base_url(), "Kamu belum login!.");
            die;
        }
        if (url() !== 'logout') {
            menu();
        }
    }



    public function index(): string
    {
        return view('home', ['judul' => "Home"]);
    }

    public function delete()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $id = clear($this->request->getVar('id'));

        $db = db($tabel);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }

        $db->where('id', $id);
        if ($db->delete()) {
            sukses_js("Delete data sukses.");
        } else {
            sukses_js("Delete data gagal!.");
        }
    }

    public function logout()
    {
        session()->remove('id');

        sukses(base_url(), 'Logout sukses!.');
    }
    public function switch_tema()
    {
        $db = db('settings');
        $q = $db->where('setting', 'Tema')->get()->getRowArray();
        $q['value'] = ($q['value'] == 'dark' ? 'light' : 'dark');

        $db->where('id', $q['id']);
        if ($db->update($q)) {
            sukses_js('Update tema berhasil.');
        } else {
            gagal_js('Update tema gagal!.');
        }
    }

    public function statistik()
    {
        $tahun = clear($this->request->getVar('tahun'));

        $db_masuk = db("penjualan");
        $db_keluar = db("pengeluaran");

        $masuk = $db_masuk->get()->getResultArray();
        $keluar = $db_keluar->get()->getResultArray();

        $data_masuk = [];
        $data_keluar = [];


        // mencari tahun pemasukan
        foreach ($masuk as $i) {
            if ($tahun == 'All') {
                $data_masuk[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_masuk[] = $i;
                }
            }
        }

        // mencari tahun pengeluaran
        foreach ($keluar as $i) {
            if ($tahun == 'All') {
                $data_keluar[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_keluar[] = $i;
                }
            }
        }

        // mencari bulan

        $detail_masuk = [];

        // masuk
        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_masuk as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    $temp[] = $i;
                    $total +=  $i['total'];
                }
            }
            $detail_masuk[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        $detail_keluar = [];
        // keluar
        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_keluar as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    $temp[] = $i;
                    $total +=  $i['total'];
                }
            }

            $detail_keluar[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        sukses_js('Connection success.', $detail_masuk, $detail_keluar);
    }

    public function bisyaroh()
    {
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));

        $dbu = db('user');
        $users = $dbu->where('role', 'Admin')->get()->getResultArray();

        $res = [];
        foreach ($users as $u) {
            $db_masuk = db("penjualan");
            $db_keluar = db("pengeluaran");


            $masuk = $db_masuk->whereNotIn('ket', ['Hutang'])->where('petugas', $u['nama'])->orderBy('tgl', 'ASC')->get()->getResultArray();
            $keluar = $db_keluar->where('petugas', $u['nama'])->orderBy('tgl', 'ASC')->get()->getResultArray();

            $data = [];
            $total = 0;


            // mencari tahun pemasukan
            foreach ($masuk as $i) {
                if ($tahun == date('Y', $i['tgl']) && $bulan == date('m', $i['tgl'])) {
                    $i['kategori'] = "Penjualan";
                    $data[] = $i;
                    $total++;
                }
            }

            // mencari tahun pengeluaran
            foreach ($keluar as $i) {
                if ($tahun == date('Y', $i['tgl']) && $bulan == date('m', $i['tgl'])) {
                    $i['kategori'] = "Pengeluaran";
                    $data[] = $i;
                    $total++;
                }
            }
            $bisyaroh = options('Bisyaroh');

            $u['bisyaroh'] = (int)$bisyaroh[0]['value'] * $total;
            $u['data'] = $data;
            $res[] = $u;
        }

        sukses_js("sukses", $res, (int)$bisyaroh[0]['value']);
    }
    public function update_bisyaroh()
    {
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));
        $bisyaroh = rp_to_int(clear($this->request->getVar('bisyaroh')));

        $dbo = db('options');
        $q = $dbo->where('grup', 'Bisyaroh')->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found...');
        }

        $q['value'] = $bisyaroh;
        $dbo->where('id', $q['id']);
        $dbo->update($q);

        $dbu = db('user');
        $users = $dbu->where('role', 'Admin')->get()->getResultArray();

        $res = [];
        foreach ($users as $u) {
            $db_masuk = db("penjualan");
            $db_keluar = db("pengeluaran");


            $masuk = $db_masuk->whereNotIn('ket', ['Hutang'])->where('petugas', $u['nama'])->orderBy('tgl', 'ASC')->get()->getResultArray();
            $keluar = $db_keluar->where('petugas', $u['nama'])->orderBy('tgl', 'ASC')->get()->getResultArray();

            $data = [];
            $total = 0;


            // mencari tahun pemasukan
            foreach ($masuk as $i) {
                if ($tahun == date('Y', $i['tgl']) && $bulan == date('m', $i['tgl'])) {
                    $i['kategori'] = "Penjualan";
                    $data[] = $i;
                    $total++;
                }
            }

            // mencari tahun pengeluaran
            foreach ($keluar as $i) {
                if ($tahun == date('Y', $i['tgl']) && $bulan == date('m', $i['tgl'])) {
                    $i['kategori'] = "Pengeluaran";
                    $data[] = $i;
                    $total++;
                }
            }
            $bisyaroh = options('Bisyaroh');

            $u['bisyaroh'] = (int)$bisyaroh[0]['value'] * $total;
            $u['data'] = $data;
            $res[] = $u;
        }

        sukses_js("sukses", $res, (int)$bisyaroh[0]['value']);
    }
    public function koperasi()
    {
        $db = db('koperasi');
        $val = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
        sukses_js("Sukses", $val);
    }
    public function add_koperasi()
    {

        $jml = rp_to_int(clear($this->request->getVar('jml')));

        $db = db('koperasi');

        $data = [
            'tgl' => time(),
            'jml' => $jml,
            'pj' => user()['nama']
        ];

        if ($db->insert($data)) {
            $val = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
            sukses_js("Sukses", $val);
        }
        gagal_js('Update gagal...');
    }
    public function update_koperasi()
    {

        $jml = rp_to_int(clear($this->request->getVar('jml')));
        $id = clear($this->request->getVar('id'));

        $db = db('koperasi');
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js("Id not found...");
        }
        $q['jml'] = $jml;

        $db->where('id', $q['id']);
        if ($db->update($q)) {
            $val = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
            sukses_js("Sukses", $val);
        }
        gagal_js('Update gagal...');
    }
}
