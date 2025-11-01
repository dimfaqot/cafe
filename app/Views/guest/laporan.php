<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul; ?></title>
    <style>
        body {
            font-size: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div style="text-align: center;margin-bottom:10px"><?= $logo; ?></div>
    <h4 style="text-align: center;"><?= $judul; ?></h4>
    <?php if ($order !== "Tahunan"): ?>
        <h4>A. RANGKUMAN</h4>
        <table style="width: 100%;">
            <tr>
                <th>NO.</th>
                <th>BULAN</th>
                <th>MASUK</th>
                <th>KELUAR</th>
                <th>SALDO</th>
            </tr>
            <?php $tot_rangkuman = 0; ?>
            <?php foreach ($rangkuman as $k => $i): ?>
                <?php $tot_rangkuman += $i['total']; ?>
                <tr>
                    <td style="text-align:center;"><?= ($k + 1); ?></td>
                    <td><?= $i['tgl']; ?></td>
                    <td style="text-align: right;"><?= angka($i['masuk']); ?></td>
                    <td style="text-align: right;"><?= angka($i['keluar']); ?></td>
                    <td style="text-align: right;"><?= angka($i['total']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4" style="text-align: center;">TOTAL</th>
                <th style="text-align: right;"><?= angka($tot_rangkuman); ?></th>
            </tr>
        </table>
    <?php endif; ?>

    <?php if ($order == "All"): ?>
        <h4>B. PEMASUKAN</h4>
        <table style="width: 100%;">
            <tr>
                <th>No.</th>
                <th>Tgl</th>
                <th>Barang</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Diskon</th>
                <th>Biaya</th>
            </tr>
            <?php foreach ($data['transaksi']['data'] as $k => $i): ?>
                <tr>
                    <td style="text-align: center;"><?= ($k + 1); ?></td>
                    <td style="text-align: center;"><?= date('d-m-Y', $i['tgl']); ?></td>
                    <td><?= $i['barang']; ?></td>
                    <td style="text-align: right;"><?= angka($i['harga']); ?></td>
                    <td style="text-align: right;"><?= angka($i['qty']); ?></td>
                    <td style="text-align: right;"><?= angka($i['diskon']); ?></td>
                    <td style="text-align: right;"><?= angka($i['biaya']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <h4>C. PENGELUARAN</h4>
        <table style="width: 100%;">
            <tr>
                <th>No.</th>
                <th>Tgl</th>
                <th>Barang</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Diskon</th>
                <th>Biaya</th>
            </tr>
            <?php foreach ($data['pengeluaran']['data'] as $k => $i): ?>
                <tr>
                    <td style="text-align: center;"><?= ($k + 1); ?></td>
                    <td style="text-align: center;"><?= date('d-m-Y', $i['tgl']); ?></td>
                    <td><?= $i['barang']; ?></td>
                    <td style="text-align: right;"><?= angka($i['harga']); ?></td>
                    <td style="text-align: right;"><?= angka($i['qty']); ?></td>
                    <td style="text-align: right;"><?= angka($i['diskon']); ?></td>
                    <td style="text-align: right;"><?= angka($i['biaya']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <?php if ($order == "Harian"): ?>
        <h4>B. DETAIL</h4>
        <table style="width: 100%;">
            <tr>
                <th>Tgl</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Saldo</th>
            </tr>
            <?php for ($i = 0; $i < count($data['bulan_ini']['data']); $i++): ?>
                <tr>
                    <td style="text-align: center;"><?= ($i + 1); ?></td>
                    <td style="text-align: right;"><?= angka($data['bulan_ini']['data'][$i]['transaksi']); ?></td>
                    <td style="text-align: right;"><?= angka($data['bulan_ini']['data'][$i]['pengeluaran']); ?></td>
                    <td style="text-align: right;"><?= angka($data['bulan_ini']['data'][$i]['total']); ?></td>
                </tr>
            <?php endfor; ?>
        </table>
    <?php endif; ?>
    <?php if ($order == "Bulanan"): ?>
        <h4>B. DETAIL</h4>
        <table style="width: 100%;">
            <tr>
                <th>No.</th>
                <th>Bulan</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Saldo</th>
            </tr>
            <?php $tot_rangkuman = 0; ?>
            <?php foreach ($rangkuman as $k => $i): ?>
                <?php $tot_rangkuman += $i['total']; ?>
                <tr>
                    <td style="text-align:center;"><?= ($k + 1); ?></td>
                    <td><?= $i['tgl']; ?></td>
                    <td style="text-align: right;"><?= angka($i['masuk']); ?></td>
                    <td style="text-align: right;"><?= angka($i['keluar']); ?></td>
                    <td style="text-align: right;"><?= angka($i['total']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4" style="text-align: center;">TOTAL</th>
                <th style="text-align: right;"><?= angka($tot_rangkuman); ?></th>
            </tr>
        </table>
    <?php endif; ?>
    <?php if ($order == "Tahunan"): ?>
        <h4>A. RANGKUMAN</h4>
        <table style="width: 100%;">
            <tr>
                <th>No.</th>
                <th>Tahun</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Saldo</th>
            </tr>
            <?php $tot_rangkuman = 0; ?>
            <?php foreach ($data as $k => $i): ?>
                <?php $tot_rangkuman += $i['total']; ?>
                <tr>
                    <td style="text-align:center;"><?= ($k + 1); ?></td>
                    <td style="text-align: center;"><?= $i['tgl']; ?></td>
                    <td style="text-align: right;"><?= angka($i['masuk']); ?></td>
                    <td style="text-align: right;"><?= angka($i['keluar']); ?></td>
                    <td style="text-align: right;"><?= angka($i['total']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4" style="text-align: center;">TOTAL</th>
                <th style="text-align: right;"><?= angka($tot_rangkuman); ?></th>
            </tr>
        </table>
    <?php endif; ?>
</body>

</html>