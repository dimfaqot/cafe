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

        foreach ($q as $i) {
            if (date('m', $i['tgl']) == date('m') && date('Y', $i['tgl']) == date('Y') && date('d', $i['tgl']) == date('d')) {
                $data[] = $i;
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
        $uang_pembayaran = (int)clear($this->request->getVar('uang_pembayaran'));
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
                    $barang['qty'] += (int)$i['qty'];
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
}
