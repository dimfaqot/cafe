<?php

namespace App\Controllers;

class Pengeluaran extends BaseController
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

        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        $data = [];

        foreach ($q as $k => $i) {
            if (user()['role'] == "Advisor" && $k < 200) {
                $data[] = $i;
            } else {
                if (date('m', $i['tgl']) == date('m') && date('Y', $i['tgl']) == date('Y') && date('d', $i['tgl']) == date('d')) {
                    $data[] = $i;
                }
            }
        }

        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));
        $db = db('barang');
        $q = $db->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();

        sukses_js("Sukses", $q);
    }

    public function transaksi()
    {
        $data = json_decode(json_encode($this->request->getVar('daftar_transaksi')), true);
        $uang_pembayaran = rp_to_int(clear($this->request->getVar('uang_pembayaran')));
        $nama_penjual = upper_first(clear($this->request->getVar('nama_penjual')));
        $petugas = upper_first(clear($this->request->getVar('petugas')));
        $kategori = upper_first(clear($this->request->getVar('kategori')));

        $total = 0;
        foreach ($data as $i) {
            $total += (int)$i['total'];
        }

        if ($uang_pembayaran < $total) {
            gagal_js("Uang kurang!.");
        }

        $db = db('pengeluaran');
        $dbb = db('barang');

        $total_2 = 0;
        $err = [];
        foreach ($data as $i) {
            $i['qty'] = rp_to_int($i['qty']);
            $i['diskon'] = rp_to_int($i['diskon']);
            $i['harga'] = rp_to_int($i['harga']);
            $i['total'] = rp_to_int($i['total']);
            $data = [
                'tgl' => time(),
                'kategori' => $kategori,
                'penjual' => $nama_penjual,
                'petugas' => $petugas,
                'barang' => $i['barang'],
                'qty' => $i['qty'],
                'diskon' => $i['diskon'],
                'harga' => $i['harga'],
                'total' => $i['total']
            ];


            if ($db->insert($data)) {
                $total_2 += $i['total'];

                $barang = $dbb->where('id', $i['id'])->get()->getRowArray();

                if ($barang) {
                    $barang['qty'] += $i['qty'];
                    $dbb->where('id', $barang['id']);
                    $dbb->update($barang);
                }
            } else {
                $err[] = $i['barang'];
            }
        }

        sukses_js("Sukses", $uang_pembayaran - $total_2, implode(", ", $err));
    }

    public function user()
    {
        $val = clear(upper_first($this->request->getVar('val')));

        $db = db('user');
        $q = $db->whereIn("role", ["Admin"])->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }

    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $petugas = clear($this->request->getVar('petugas'));
        $barang = clear($this->request->getVar('barang'));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $qty = rp_to_int(clear($this->request->getVar('qty')));
        $total = rp_to_int(clear($this->request->getVar('total')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['tabel']), "Id not found...");
        }

        $q['petugas'] = $petugas;
        $q['barang'] = $barang;
        $q['harga'] = $harga;
        $q['qty'] = $qty;
        $q['total'] = $total;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['tabel']), "Sukses...");
        } else {
            gagal(base_url(menu()['tabel']), "Gagal...");
        }
    }
}
