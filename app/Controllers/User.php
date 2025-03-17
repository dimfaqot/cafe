<?php

namespace App\Controllers;

class User extends BaseController
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

        $q = $db->orderBy('role', 'ASC')->orderBy('nama', 'ASC')->get()->getResultArray();
        $data = [];

        foreach ($q as $i) {
            $i['link'] = base_url('auth/') . encode_jwt(['id' => $i['id']]);
            $data[] = $i;
        }
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $nama = clear(upper_first($this->request->getVar('nama')));
        $role = clear(upper_first($this->request->getVar('role')));
        $hp = clear(upper_first($this->request->getVar('hp')));

        $db = db(menu()['tabel']);
        if ($db->where('nama', $nama)->where('role', $role)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Nama sudah ada!.");
        }
        if ($db->where('hp', $hp)->where('role', $role)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Nama sudah ada!.");
        }

        $data = [
            'nama' => $nama,
            'hp' => $hp,
            'role' => $role
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
        $nama = clear(upper_first($this->request->getVar('nama')));
        $role = clear(upper_first($this->request->getVar('role')));
        $hp = clear(upper_first($this->request->getVar('hp')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('nama', $nama)->where('role', $role)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Nama sudah ada!.");
        }
        if ($db->whereNotIn('id', [$id])->where('hp', $hp)->where('role', $role)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Hp sudah ada!.");
        }


        $q['nama'] = $nama;
        $q['role'] = $role;
        $q['hp'] = $hp;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
