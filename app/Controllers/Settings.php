<?php

namespace App\Controllers;

class Settings extends BaseController
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
        $data = db(menu()['tabel'])->orderBy("nama", "ASC")->get()->getResultArray();
        return view(menu()['controller'] . '/' . menu()['controller'] . '_' . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }
    public function add()
    {
        $input = [
            'nama'       => strtolower(clear($this->request->getVar('nama'))),
            'value'       => clear($this->request->getVar('value'))
        ];


        // Cek duplikat
        if (db(menu()['tabel'])->where('nama', $input['nama'])->countAllResults() > 0) {
            gagal(base_url(menu()['controller']), 'Setting existed');
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

        $q = [
            'nama'       => strtolower(clear($this->request->getVar('nama'))),
            'value'       => clear($this->request->getVar('value'))
        ];

        if ((db(menu()['tabel'])->whereNotIn('id', [$id]))->where("nama", $q['nama'])->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Setting existed");
        }

        // Simpan data
        db(menu()['tabel'])->where('id', $id)->update($q)
            ? sukses(base_url(menu()['controller']), 'Sukses')
            : gagal(base_url(menu()['controller']), 'Gagal');
    }

    public function copy_table()
    {
        $tabel = clear($this->request->getVar('tabel'));

        $db_old = db(($tabel == "transaksi" || $tabel == "hutang" ? "penjualan" : $tabel), getenv('OLD_DB'));
        $old = $db_old->orderBy('id', 'ASC')->get()->getResultArray();
        $insert = [];
        $db = db_connect();
        $db->transStart();
        foreach ($old as $k => $i) {
            if ($tabel == "pengeluaran") {
                $data = [
                    'tgl' => $i['tgl'],
                    'jenis' => $i['kategori'],
                    'barang' => $i['barang'],
                    'barang_id' => 0,
                    'harga' => $i['harga'],
                    'qty' => $i['qty'],
                    'total' => $i['qty'] * $i['harga'],
                    'diskon' => $i['diskon'],
                    'biaya' => $i['total'],
                    'pj' => $i['petugas'],
                    'petugas' => $i['petugas'],
                    'updated_at' => $i['tgl']
                ];
                $insert[] = $data;
            } elseif ($tabel == "transaksi" && $i['ket'] !== "Hutang") {

                $data = [
                    'tgl' => $i['tgl'],
                    'jenis' => '',
                    'barang' => $i['barang'],
                    'barang_id' => 0,
                    'harga' => $i['harga'],
                    'qty' => $i['qty'],
                    'total' => $i['qty'] * $i['harga'],
                    'diskon' => $i['diskon'],
                    'biaya' => $i['total'],
                    'petugas' => $i['petugas']
                ];
                $insert[] = $data;
            } elseif ($tabel == "hutang" && $i['ket'] == 'Hutang') {
                $no_nota = next_invoice('hutang');
                $data = [
                    'no_nota' => $no_nota,
                    'tgl' => $i['tgl'],
                    'jenis' => '',
                    'barang' => $i['barang'],
                    'barang_id' => 0,
                    'harga' => $i['harga'],
                    'qty' => $i['qty'],
                    'total' => $i['qty'] * $i['harga'],
                    'diskon' => $i['diskon'],
                    'biaya' => $i['total'],
                    'petugas' => $i['petugas'],
                    'nama' => $i['pembeli'],
                    'user_id' => $i['user_id'],
                    'tipe' => ''
                ];
                $insert[] = $data;
                // db($tabel, 'cafe')->insert($i);
            }
        }
        // dd(count($insert));
        // $last = array_slice($insert, 6000, 500, true);
        // foreach ($insert as $i) {

        //     db($tabel, 'cafe')->insert($i);
        // }
        $db->transComplete();

        if (!$db->transStatus()) {
            gagal_js('Copy gagal');
        }

        sukses_js('Copy sukses');
    }
}
