<?php

namespace App\Controllers;

class Barang extends BaseController
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
        $db = db(menu()['tabel']);

        $data = $db->orderBy('updated_at', 'DESC')->get()->getResultArray();


        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $qty = (int)str_replace(".", "", clear($this->request->getVar('qty')));
        $harga = (int)str_replace(".", "", clear($this->request->getVar('harga')));

        $db = db(menu()['tabel']);
        if ($db->where('barang', $barang)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Barang sudah ada!.");
        }

        $data = [
            'tgl' => time(),
            'barang' => $barang,
            'kategori' => $kategori,
            'qty' => ($kategori !== "Barang" ? 0 : $qty),
            'harga' => ($kategori !== "Barang" ? 0 : $harga),
            'updated_at' => time(),
            'petugas' => user()['nama']
        ];

        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), "Tambah data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $barang = upper_first(clear($this->request->getVar('barang')));
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $qty = (int)str_replace(".", "", clear($this->request->getVar('qty')));
        $harga = (int)str_replace(".", "", clear($this->request->getVar('harga')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('barang', $barang)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Barang sudah ada!.");
        }


        $q['barang'] = $barang;
        $q['qty'] =  ($kategori !== "Barang" ? 0 : $qty);
        $q['harga'] =  ($kategori !== "Barang" ? 0 : $harga);
        $q['updated_at'] = time();
        $q['petugas'] = user()['nama'];

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
