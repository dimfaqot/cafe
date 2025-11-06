<?php

namespace App\Controllers;

class Transaksi extends BaseController
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

        $data = db(menu()['tabel'])->orderBy("tgl", "DESC")->get()->getResultArray();
        return view(menu()['controller'] . '/' . menu()['controller'] . "_" . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }

    public function bayar()
    {
        $super_total = json_decode(json_encode($this->request->getVar('super_total')), true);
        $datas = json_decode(json_encode($this->request->getVar('datas')), true);
        $uang = angka_to_int(clear($this->request->getVar('uang')));
        $order = clear($this->request->getVar('order'));

        $col_id = ($order == "pay" ? "barang_id" : "id");

        if ($uang < $super_total['biaya']) {
            gagal_js("Uang kurang");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $no_nota = next_invoice();

        $tgl = time();

        foreach ($datas as $i) {
            if (!$db->table('transaksi')->insert([
                "tgl" => $tgl,
                "jenis" => $i['jenis'],
                "barang" => $i['barang'],
                "barang_id" => $i[$col_id],
                "harga" => $i['harga'],
                "qty" => $i['qty'],
                "total" => $i['total'],
                "diskon" => $i['diskon'],
                "biaya" => $i['biaya'],
                "petugas" => user()['nama']
            ])) {
                gagal_js("Insert transaksi gagal");
            }

            $barang = db('barang')->where('id', $i[$col_id])->get()->getRowArray();
            if (!$barang) {
                gagal_js("Id " . $i['barang'] . " not found");
            }

            if ($order !== "pay") {
                if ($barang['tipe'] == "Mix" && $barang['link'] !== '') {
                    $exp = explode(",", $barang['link']);

                    foreach ($exp as $x) {
                        $val = db('barang')->where('barang', $x)->get()->getRowArray();

                        if (!$val) {
                            gagal_js("Link barang null");
                        }

                        $val['qty'] -= (int)$i['qty'];
                        if ($val['qty'] < 0) {
                            gagal_js('Stok kurang');
                        }

                        if (!db('barang')->where('id', $val['id'])->update($val)) {
                            gagal_js("Update stok gagal");
                        }
                    }
                }

                if ($barang['tipe'] == "Count") {
                    $barang['qty'] -= (int)$i['qty'];
                    if ($barang['qty'] < 0) {
                        gagal_js('Stok kurang');
                    }
                    if (!db('barang')->where('id', $barang['id'])->update($barang)) {
                        gagal_js("Update stok gagal");
                    }
                }
            }


            if (!$db->table('nota')->insert([
                "no_nota" => $no_nota,
                "tgl" => $tgl,
                "jenis" => $i['jenis'],
                "barang" => $i['barang'],
                "barang_id" => $i[$col_id],
                "harga" => $i['harga'],
                "qty" => $i['qty'],
                "total" => $i['total'],
                "diskon" => $i['diskon'],
                "biaya" => $i['biaya'],
                "petugas" => user()['nama'],
                "uang" => $uang,
            ])) {
                gagal_js("Insert nota gagal");
            }

            if ($order == "pay") {
                if (!db('hutang')->where('id', $i['id'])->delete()) {
                    gagal_js("Delete hutang gagal");
                }
            }
        }


        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses", str_replace("/", "-", $no_nota))
            : gagal_js("Gagal");
    }
    public function add_hutang()
    {
        $datas = json_decode(json_encode($this->request->getVar('datas')), true);
        $nama = upper_first(clear($this->request->getVar('nama')));
        $id = clear($this->request->getVar('id'));

        $db = \Config\Database::connect();
        $db->transStart();

        $nota = next_invoice("hutang");

        $tgl = time();

        foreach ($datas as $i) {
            $db->table('hutang')->insert([
                "no_nota" => $nota,
                "tgl" => $tgl,
                "jenis" => $i['jenis'],
                "barang" => $i['barang'],
                "barang_id" => $i['id'],
                "harga" => $i['harga'],
                "qty" => $i['qty'],
                "total" => $i['total'],
                "diskon" => $i['diskon'],
                "biaya" => $i['biaya'],
                "petugas" => user()['nama'],
                "nama" => $nama,
                "user_id" => $id
            ]);

            $barang = db('barang')->where('id', $i['id'])->get()->getRowArray();
            if (!$barang) {
                gagal_js("Id " . $i['barang'] . " not found");
            }
            if ($barang['tipe'] == "Mix" && $barang['link'] !== '') {
                $exp = explode(",", $barang['link']);

                foreach ($exp as $x) {
                    $val = db('barang')->where('barang', $x)->get()->getRowArray();

                    if (!$val) {
                        gagal_js("Link barang null");
                    }

                    $val['qty'] -= (int)$i['qty'];
                    if ($val['qty'] < 0) {
                        gagal_js('Stok kurang');
                    }

                    if (!db('barang')->where('id', $val['id'])->update($val)) {
                        gagal_js("Update stok gagal");
                    }
                }
            }

            if ($barang['tipe'] == "Count") {
                $barang['qty'] -= (int)$i['qty'];
                if ($barang['qty'] < 0) {
                    gagal_js('Stok kurang');
                }
                if (!db('barang')->where('id', $barang['id'])->update($barang)) {
                    gagal_js("Update stok gagal");
                }
            }
        }


        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses")
            : gagal_js("Gagal");
    }


    public function cari_user()
    {
        $text = clear($this->request->getVar("text"));
        $data = db('user')->like("nama", $text, "both")->orderBy('nama', 'ASC')->limit(7)->get()->getResultArray();

        $res = [];
        foreach ($data as $i) {
            $i['hutang'] = 0;
            $val = db('hutang')
                ->where('user_id', $i['id'])
                ->get()
                ->getResultArray();
            if ($val) {
                $i['hutang'] = array_sum(array_column($val, 'biaya'));
            }
            $res[] = $i;
        }
        sukses_js("Ok", $res);
    }
    public function cari_barang()
    {
        $text = clear($this->request->getVar("text"));
        $jenis = json_decode(json_encode($this->request->getVar("jenis")), true);
        $data = db('barang')->whereIn('jenis', $jenis)->like("barang", $text, "both")->orderBy('barang', 'ASC')->limit(7)->get()->getResultArray();

        sukses_js("Ok", $data);
    }
    public function add_user()
    {
        $input = [
            "nama" => upper_first(clear($this->request->getVar("nama"))),
            "wa" => clear($this->request->getVar("wa")),
            "role" => "Member",
            "username" => random_string(4),
            "password" => password_hash(settings("password")['value'], PASSWORD_DEFAULT)
        ];

        $nama = db('user')->where('nama', $input['nama'])->get()->getRowArray();

        if ($nama) {
            gagal_js('Nama existed');
        }
        $wa = db('user')->where('wa', $input['wa'])->get()->getRowArray();

        if ($wa) {
            gagal_js('W.a existed');
        }

        db("user")->insert($input)
            ? sukses_js('Sukses')
            : gagal_js('Gagal');
    }

    public function list()
    {
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));
        $jenis = clear($this->request->getVar('jenis'));
        $options = json_decode(json_encode($this->request->getVar('options')), true);

        $db = db('transaksi');
        $db->select('*');
        if ($jenis !== "All") {
            $db->where('jenis', $jenis);
        }
        $data = $db->orderBy('tgl', 'DESC')
            ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
            ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
            ->get()
            ->getResultArray();

        $total = array_sum(array_column($data, 'biaya'));


        sukses_js("Ok", $data, $total);
    }
    public function delete()
    {
        $id = clear($this->request->getVar('id'));
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));
        $jenis = clear($this->request->getVar('jenis'));
        $options = json_decode(json_encode($this->request->getVar('options')), true);

        $db = \Config\Database::connect();
        $db->transStart();

        $data = db('transaksi')->where('id', $id)->get()->getRowArray();

        if (!$data) {
            gagal_js("Id not found");
        }

        $barang = db('barang')->where('id', $data['barang_id'])->get()->getRowArray();

        if (!$barang) {
            gagal_js("Barang id not found");
        }

        if ($barang['tipe'] == "Count") {
            $barang['qty'] += $data['qty'];

            if (!db('barang')->where('id', $barang['id'])->update($barang)) {
                gagal_js("Update qty gagal");
            }
        }

        if (!db('transaksi')->where('id', $id)->delete()) {
            gagal_js("Delete transaksi gagal");
        }


        $dbt = db('transaksi');
        $dbt->select('*');
        $data = $dbt->orderBy('tgl', 'DESC')
            ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
            ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
            ->get()
            ->getResultArray();

        $total = array_sum(array_column($data, 'biaya'));


        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses", $data, $total)
            : gagal_js("Gagal");
    }
    public function cashier()
    {
        $data = db('hutang')->groupBy('no_nota')->orderBy('tgl', 'DESC')->orderBy('nama', 'ASC')->limit(100)->get()->getResultArray();
        sukses_js('Ok', $data);
    }
    public function add_item()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $data = db('hutang')->where('no_nota', $no_nota)->orderBy('barang', 'ASC')->get()->getResultArray();
        sukses_js('Ok', $data);
    }
    public function btn_add_item()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $datas = json_decode(json_encode($this->request->getVar('datas')), true);
        $db = \Config\Database::connect();
        $db->transStart();
        $pembeli = [];
        foreach ($datas as $i) {
            if (key_exists('ket', $i) && $i['ket'] == "old" && count($pembeli) == 0) {
                $pembeli = $i;
                break;
            }
        }
        foreach ($datas as $i) {
            if (!key_exists('ket', $i)) {
                $val = [
                    "no_nota" => $no_nota,
                    "tgl" => time(),
                    "jenis" => $i['jenis'],
                    "tipe" => $i['tipe'],
                    "barang" => $i['barang'],
                    "barang_id" => $i['id'],
                    "harga" => $i['harga'],
                    "qty" => $i['qty'],
                    "total" => $i['total'],
                    "diskon" => $i['diskon'],
                    "biaya" => $i['biaya'],
                    "petugas" => user()['nama'],
                    "nama" => $pembeli['nama'],
                    "user_id" => $pembeli['user_id']
                ];

                if (db('hutang')->insert($val)) {
                    $barang = db('barang')->where('id', $i['id'])->get()->getRowArray();
                    if (!$barang) {
                        gagal_js("Barang not found");
                    }

                    if ($barang['tipe'] == "Count") {
                        $barang['qty'] -= $i['qty'];

                        if (!db('barang')->where('id', $barang['id'])->update($barang)) {
                            gagal_js("Update qty gagal");
                        }
                    }
                }
            }
        }

        $dbt = db('hutang');
        $dbt->select('*');
        $res = $dbt->orderBy('barang', 'ASC')->where('no_nota', $no_nota)
            ->get()
            ->getResultArray();

        $total = array_sum(array_column($res, 'biaya'));
        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses", $res, $total)
            : gagal_js("Gagal");
    }
}
