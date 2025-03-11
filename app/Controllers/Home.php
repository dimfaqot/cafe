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
}
