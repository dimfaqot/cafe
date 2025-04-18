<?php

namespace App\Controllers;

class Hutang extends BaseController
{
    function __construct()
    {
        helper('functions');
        if (!session('id')) {
            gagal(base_url(), "Kamu belum login!.");
            die;
        }
        menu();
    }


    public function index(): string
    {
        $dbu = db('user');
        $users = $dbu->orderBy('nama', 'ASC')->get()->getResultArray();

        $db = db('penjualan');
        $data = [];
        foreach ($users as $u) {
            $q = $db->where('user_id', $u['id'])->where('ket', 'Hutang')->orderBy('tgl', 'ASC')->get()->getResultArray();

            $total = 0;
            $temp_data = [];
            foreach ($q as $i) {
                $total += (int)$i['total'];
                $temp_data[] = $i;
            }
            $data[$u['id']] = ['user' => $u, 'total' => $total, 'data' => $temp_data];
        }
        return view(menu()['controller'], ['judul' => menu()['menu'], 'users' => $users, 'data' => $data]);
    }

    public function bayar()
    {
        $user_id = clear($this->request->getVar('user_id'));
        $metode = clear($this->request->getVar('metode'));

        $db = db('penjualan');
        $data = [];

        $q = $db->where('user_id', $user_id)->where('ket', 'Hutang')->orderBy('tgl', 'ASC')->get()->getResultArray();


        $err = [];
        foreach ($q as $i) {
            $i['ket'] = $metode;
            $i['petugas'] = user()['nama'];
            $db->where('id', $i['id']);
            if (!$db->update($i)) {
                $err[] = $i['barang'];
            }
        }

        sukses_js("Sukses", implode(", ", $err));
    }
}
