<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>


<div class="input-group input-group-sm mb-3">
    <span class="input-group-text bg_main border_main text_main">Cari Data</span>
    <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
</div>
<table class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Hutang</th>
        </tr>
    </thead>
    <tbody class="tabel_search">
        <?php $total = 0; ?>
        <?php foreach ($users as $k => $i): ?>
            <?php $total = (int)$data[$i['id']]['total']; ?>
            <tr class="detail_hutang" data-id="<?= $i['id']; ?>">
                <th><?= $k + 1; ?></th>
                <td><?= $i['nama']; ?></td>
                <td class="text-end"><?= angka($data[$i['id']]['total']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th class="text-center" colspan="2">TOTAL</th>
            <th class="text-end"><?= angka($total); ?></th>
        </tr>
    </tbody>
</table>


<script>
    let data = <?= json_encode($data); ?>;
    $(document).on("click", ".detail_hutang", function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        val = data[id];
        let html = `<div class="container">
        <h6>Nama: ${val.user.nama} | Hutang: ${angka(val.total)}</h6>`;

        html += `<table class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">Tgl</th>
            <th class="text-center">Barang</th>
            <th class="text-center">Total</th>
        </tr>
    </thead>
    <tbody>`;

        val.data.forEach((e, i) => {
            html += `<tr>
              <th>${(i+1)}</th>
              <td class="text-center">${time_php_to_js(e.tgl)}</td>
              <td>${e.barang}</td>
              <td class="text-end">${e.total}</td>
          </tr>`;

        });
        html += `<tr>
            <th class="text-center" colspan="3">TOTAL</th>
            <th class="text-end">${angka(val.total)}</th>
        </tr>
    </tbody>
</table>`;
        html += `<div class="d-grid">
                    <a href="" class="btn btn-sm bg_secondary text_main btn_confirm_bayar mb-2" data-id="${val.user.id}"><i class="fa-solid fa-cash-register"></i> Bayar</a>
                    <a class="btn btn-sm btn-success btn_whatsapp" data-user_id="${val.user.id}" data-hp="${val.user.hp}" data-nama="${val.user.nama}"><i class="fa-brands fa-whatsapp"></i> Whatsapp</a>
                </div>`;
        html += `</div>`;

        popupButton.html(html);
    })

    $(document).on('click', '.btn_whatsapp', function(e) {
        e.preventDefault();
        let nama = $(this).data('nama');
        let user_id = $(this).data('user_id');
        let jwt = $(this).data('jwt');
        let no_hp = "62";
        no_hp += $(this).data('hp').substring(1);

        let data_transaksi = data[user_id].data;

        let text = "_Assalamualaikum Wr. Wb._%0a";
        text += "Yth. *" + nama + '*%0a%0a';
        text += 'Tagihan Anda di Hayu Playground:%0a%0a';
        text += '*No. -- Tgl -- Barang -- Qty -- Harga*%0a'

        let x = 1;
        let total = 0;
        data_transaksi.forEach((e, i) => {
            total += parseInt(e.total);
            text += (x++) + '. ' + time_php_to_js(e.tgl) + ' - ' + e.barang + ' - ' + e.qty + ' - ' + angka(e.total) + '%0a';

        })
        text += '%0a';
        text += "*TOTAL: " + angka(total) + "*%0a%0a";
        text += "*_Mohon segera dibayar njihhh..._*%0a";
        text += "_Wassalamualaikum Wr. Wb._%0a%0a";
        text += 'Petugas%0a%0a';
        text += '<?= user()['nama']; ?>';
        text += "%0a%0a";
        text += "_(*)Pesan ini dikirim oleh sistem, jadi mohon maklum dan ampun tersinggung njih._";
        text += "%0a%0a";
        text += "Info lebih lengkap klik: %0a%0a";
        text += "https://cafe.walisongosragen.com/";


        // let url = "https://api.whatsapp.com/send/?phone=" + no_hp + "&text=" + text;
        let url = "whatsapp://send/?phone=" + no_hp + "&text=" + text;

        location.href = url;
        // window.open(url);
    });


    $(document).on('click', '.btn_confirm_bayar', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let myModal = document.getElementById("fullscreen");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.hide();

        let html = `<div class="container">
       <div class='text-center'>
            <div>Yakin bayar?</div>
                <button data-id="${id}" class="link_secondary btn_bayar mt-2 px-3 border_main rounded">Cash</button>
                <button data-id="${id}" class="link_main btn_bayar mt-2 px-3 border_main rounded">Qris</button>
                <button data-id="${id}" class="link_secondary btn_bayar mt-2 px-3 border_main rounded">Tap</button>
        </div>
        </div>`

        popupButton.html(html);
    });
    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();
        let user_id = $(this).data("id");
        let metode = $(this).text();
        post("hutang/bayar", {
            user_id,
            metode
        }).then(res => {
            message(res.status, res.message);
            setTimeout(() => {
                location.reload();
            }, 1200);
        })
    });
</script>
<?= $this->endSection() ?>