<?php

namespace App\Controllers;

class Barang extends BaseController
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
        $val = db(menu()['tabel'])->orderBy("barang", "ASC")->get()->getResultArray();
        $data = [];

        foreach ($val as $i) {
            if ($i['link'] == "") {
                $i['barangs'] = "";
            } else {

                $temp_barangs = [];
                $ids = explode(",", $i['link']);

                foreach ($ids as $t) {
                    foreach ($val as $x) {
                        if ($t == $x['id']) {
                            $temp_barangs[] = $x['barang'];
                        }
                    }
                }
                $i['barangs'] = implode(",", $temp_barangs);
            }
            $data[] = $i;
        }
        return view(menu()['controller'] . '/' . menu()['controller'] . "_" . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }
    public function add()
    {
        $tipe = (clear($this->request->getVar('tipe')) == "on" ? "Mix" : "Count");

        $input = [
            'jenis'      => angka_to_int(clear($this->request->getVar('jenis'))),
            'barang'       => upper_first(clear($this->request->getVar('barang'))),
            'link'       => clear($this->request->getVar('link')),
            'qty'       => 0,
            'tipe' => $tipe,
            'harga'      => angka_to_int(clear($this->request->getVar('harga')))
        ];

        $qty = angka_to_int(clear($this->request->getVar('qty')));
        if ($qty !== "") {
            $input['qty'] = $qty;
        }

        // Cek duplikat
        if (db(menu()['tabel'])->where('barang', $input['barang'])->countAllResults() > 0) {
            gagal(base_url(menu()['controller']), 'Barang existed');
        }

        // Simpan data  
        db(menu()['tabel'])->insert($input)
            ? sukses(base_url(menu()['controller']), 'Sukses')
            : gagal(base_url(menu()['controller']), 'Gagal');
    }

    public function edit()
    {
        $id = clear($this->request->getVar('id'));

        $q = db(menu()['tabel'])->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id not found");
        }
        $tipe = (clear($this->request->getVar('tipe')) == "on" ? "Mix" : "Count");

        $q = [
            'jenis'      => angka_to_int(clear($this->request->getVar('jenis'))),
            'link'       => clear($this->request->getVar('link')),
            'barang'       => upper_first(clear($this->request->getVar('barang'))),
            'tipe'       => $tipe,
            'harga'      => angka_to_int(clear($this->request->getVar('harga')))
        ];

        $qty = angka_to_int(clear($this->request->getVar('qty')));
        if ($qty !== "") {
            $q['qty'] = $qty;
        }

        if ((db(menu()['tabel'])->whereNotIn('id', [$id]))->where("barang", $q['barang'])->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Barang existed");
        }

        // Simpan data
        db(menu()['tabel'])->where('id', $id)->update($q)
            ? sukses(base_url(menu()['controller']), 'Sukses')
            : gagal(base_url(menu()['controller']), 'Gagal');
    }
}
