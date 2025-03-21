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
                    $total +=  $i['harga'];
                }
            }
            $detail_keluar[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        sukses_js('Connection success.', $detail_masuk, $detail_keluar);
    }
}
