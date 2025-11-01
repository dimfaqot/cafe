<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<div class="text-center fw-bold mb-3" style="font-size: 12px;">
    <div>WELCOME</div>
    -[<span class="bg-dark"><?= strtoupper(user()['nama']); ?></span>]-
</div>
<div class="border rounded border-secondary p-3">
    <div class="d-flex justify-content-center gap-2">
        <div class="form-floating flex-fill" style="width: 50%;">
            <select class="form-select bg-dark text-secondary tahun">
                <?php foreach (tahuns('transaksi') as $i): ?>
                    <option <?= ($i['tahun'] == date('Y') ? "selected" : ""); ?> value="<?= $i['tahun']; ?>"><?= $i['tahun']; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Tahun</label>
        </div>
        <div class="form-floating flex-fill" style="width: 50%;">
            <select class="form-select bg-dark text-secondary bulan">
                <?php foreach (bulans() as $i): ?>
                    <option <?= ($i['satuan'] == date('n') ? "selected" : ""); ?> value="<?= $i['satuan']; ?>"><?= $i['bulan']; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Bulan</label>
        </div>
    </div>
    <div class="d-flex justify-content-center gap-2 mt-3 body_menu"></div>
    <div class="body_detail mt-4"></div>
</div>


<script>
    let role = "<?= user()['role']; ?>";
    const body_menu = (order = "") => {
        let html = `
                        <a href="" data-order="transaksi" class="border data rounded-circle border-secondary text-center p-1 ${(order=="transaksi"?"bg-success":"")}" style="width:50px;height:50px;text-decoration:none;color:white;cursor:pointer">
                            <div><i class="${(order=="transaksi"?"text-light":"text-success")} fa-solid fa-arrow-turn-down"></i></div>
                            <div style="font-size: xx-small;">Masuk</div>
                        </a>
                        <a href="" data-order="pengeluaran" class="border data rounded-circle border-secondary text-center p-1 ${(order=="pengeluaran"?"bg-success":"")}" style="width:50px;height:50px;text-decoration:none;color:white;cursor:pointer">
                            <div><i class="${(order=="pengeluaran"?"text-light":"text-success")} fa-solid fa-arrow-turn-up"></i></div>
                            <div style="font-size: xx-small;">Keluar</div>
                        </a>
                        <a href="" data-order="hutang" class="border data rounded-circle border-secondary text-center p-1 ${(order=="hutang"?"bg-success":"")}" style="width:50px;height:50px;text-decoration:none;color:white;cursor:pointer">
                            <div><i class="${(order=="hutang"?"text-light":"text-success")} fa-solid fa-spinner"></i></div>
                            <div style="font-size: xx-small;">Hutang</div>
                        </a>
                        <a href="" data-order="laporan" class="border data rounded-circle border-secondary text-center p-1 ${(order=="laporan"?"bg-success":"")}" style="width:50px;height:50px;text-decoration:none;color:white;cursor:pointer">
                            <div><i class="${(order=="laporan"?"text-light":"text-success")} fa-solid fa-arrow-trend-up"></i></div>
                            <div style="font-size: xx-small;">Laporan</div>
                        </a>
                    `;
        $(".body_menu").html(html);
    }
    body_menu();
    const table = (data, total, sub_menu, order, jenis = "All") => {
        let html = `
            <div class="d-flex flex-wrap gap-2">
            <div class="form-check form-switch" style="font-size:12px">
                        <input class="form-check-input filter" data-jenis="All" data-order="${order}" type="radio" role="switch" name="sub_menu" ${(jenis=="All"?"checked":"")}>
                        <label class="form-check-label">All</label>
                </div>`;
        sub_menu.forEach(e => {
            html += `<div class="form-check form-switch" style="font-size:12px">
                        <input class="form-check-input filter" data-jenis="${e}" data-order="${order}" type="radio" role="switch" name="sub_menu" ${(e==jenis?"checked":"")}>
                        <label class="form-check-label">${e}</label>
                        </div>`;

        })
        html += `</div><hr>`;
        html += `<div class="input-group input-group-sm mb-2">
    <input type="text" class="form-control bg-dark text-light border-secondary cari" placeholder="Cari...">
</div>`;
        if (order == "laporan") {
            html += `<div class="d-grid my-2"><a href="" class="btn btn-sm btn-secondary cetak" data-jenis="${jenis}"><i class="fa-solid fa-file-pdf"></i> CETAK</a></div>`;
            if (jenis == "Tahunan" && role == "Root") {
                html += `<div class="d-grid my-2"><a href="" class="btn btn-sm btn-success backup"><i class="fa-solid fa-database"></i> BACKUP</a></div>`;

            }
            if (jenis == "All") {
                html += `
                    <div>Masuk: ${angka(data.transaksi.total)}</div>
                    <div>Keluar: ${angka(data.pengeluaran.total)}</div>
                    <div style="font-size:12px" class="fw-bold text-warning">TOTAL: ${(data.transaksi.total-data.pengeluaran.total<0?"-":"")+angka(data.transaksi.total-data.pengeluaran.total)}</div>
                `;
            } else {
                html += `<div style="font-size:12px" class="fw-bold text-warning">TOTAL: ${(total.transaksi-total.pengeluaran<0?"-":"")+angka(total.transaksi-total.pengeluaran)}</div>`;
            }

        } else {
            html += `<div style="font-size:12px" class="fw-bold text-warning">TOTAL: ${angka(total)}</div>`;
        }
        if (order == "laporan") {
            if (jenis !== "All") {
                html += `<table class="table table-sm table-dark table-bordered" style="font-size:12px">
                                <thead>
                                    <tr>
                                        <th class="text-center">${(jenis=="All"?"Tgl":jenis.slice(0,-2))}</th>
                                        <th class="text-center">Masuk</th>
                                        <th class="text-center">Keluar</th>
                                        <th class="text-center">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody class="tabel_search">`;
                data.forEach((e, i) => {
                    html += `<tr>
                                        <td class="${(jenis=="All" || jenis=="Tahunan"?"text-center":"")}">${e.tgl}</td>
                                        <td class="text-end">${angka(e.masuk)}</td>
                                        <td class="text-end">${angka(e.keluar)}</td>
                                        <td class="text-end">${(e.masuk-e.keluar<0?"-":"")}${angka(e.masuk-e.keluar)}</td>
                                    </tr>`;
                })
                html += `</tbody>
                            </table>`;

            }

        } else {
            html += `<table class="table table-sm table-dark table-bordered" style="font-size:12px">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Tgl</th>
                                    <th class="text-center">Barang</th>`;
            if (order == "hutang") {
                html += `<th class="text-center">Nama</th>`;
            } else {
                html += `<th class="text-center">Qty</th>
                                        <th class="text-center">Diskon</th>`;

            }
            html += `<th class="text-center">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="tabel_search">`;
            data.forEach((e, i) => {
                html += `<tr>
                                    <td scope="row">${(i+1)}</td>
                                    <td>${time_php_to_js(e.tgl)}</td>
                                    <td class="text-start">${e.barang}</td>`
                if (order == "hutang") {
                    html += `<td>${e.nama}</td>`;

                } else {

                    html += `<td>${angka(e.qty)}</td>
                                        <td>${angka(e.diskon)}</td>`;
                }
                html += `<td class="text-end">${(e.jenis=="Bisyaroh"?"-":angka(e.biaya))}</td>
                                </tr>`;
            })
            html += `</tbody>
                        </table>`;

        }


        return html;

    }
    $(document).on('click', '.data', function(e) {
        e.preventDefault();
        let tahun = ($(".tahun").val() == undefined || $(".tahun").val() == "" ? "<?= date('Y'); ?>" : $(".tahun").val());
        let bulan = ($(".bulan").val() == undefined || $(".bulan").val() == "" ? "<?= date('n'); ?>" : $(".bulan").val());
        let order = $(this).data("order");
        body_menu(order);

        post("home/statistik", {
            tahun,
            bulan,
            order,
            jenis: "All"
        }).then(res => {
            loading("close");
            datases = res.data;
            if (res.data.length < 1) {
                message("400", "Data tidak ada");
                return;
            }

            $(".body_detail").html(table(res.data, res.data2, res.data3, order));
        })

    });

    $(document).on('change', '.filter', function(e) {
        e.preventDefault();
        let tahun = ($(".tahun").val() == undefined || $(".tahun").val() == "" ? "<?= date('Y'); ?>" : $(".tahun").val());
        let bulan = ($(".bulan").val() == undefined || $(".bulan").val() == "" ? "<?= date('n'); ?>" : $(".bulan").val());
        let order = $(this).data("order");
        let jenis = $(this).data("jenis");

        post("home/statistik", {
            tahun,
            bulan,
            jenis,
            order
        }).then(res => {
            loading("close");

            $(".body_detail").html(table(res.data, res.data2, res.data3, order, jenis));
        })

    });

    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });
    $(document).on('click', '.cetak', function(e) {
        e.preventDefault();
        let jenis = $(this).data("jenis");
        let url = "<?= base_url("guest/laporan/"); ?>" + jenis + "/" + $(".tahun").val() + "/" + $(".bulan").val();
        window.open(url, '_blank');
    });
    $(document).on('click', '.backup', function(e) {
        e.preventDefault();
        let tahun = ($(".tahun").val() == undefined || $(".tahun").val() == "" ? "<?= date('Y'); ?>" : $(".tahun").val());
        let bulan = ($(".bulan").val() == undefined || $(".bulan").val() == "" ? "<?= date('n'); ?>" : $(".bulan").val());
        let order = "laporan";
        let jenis = "Backup";

        post("home/statistik", {
            tahun,
            bulan,
            jenis,
            order
        }).then(res => {
            loading("close");

            let html = build_html("Backup", "modal");

            html += `<div class="container"><h6 class="text-warning">TOTAL: ${angka(res.data2)}</h6>`;
            html += `<table class="table table-sm table-dark" style="font-size:12px">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">Masuk</th>
                                <th class="text-center">Keluar</th>
                                <th class="text-center">Saldo</th>
                                <th class="text-center">Lock</th>
                            </tr>
                        </thead>
                        <tbody class="tabel_unlock">`;
            res.data.forEach((e, i) => {
                html += `<tr>
                                <th scope="row">${(i+1)}</th>
                                <td>${time_php_to_js(e.tahun)}</td>
                                <td class="text-start">${e.masuk}</td>
                                <td class="text-end">${angka(e.keluar)}</td>
                                <td class="text-end">${angka(e.saldo)}</td>
                                <td class="text-center"><a class="unlock text-warning" data-keep="${e.keep}" data-id="${e.id}">${(e.keep==1?'<i class="fa-solid fa-lock text-secondary"></i>':'<i class="fa-solid fa-lock-open text-success"></i>')}</a></td>
                            </tr>`;
            })
            html += `</tbody>
                    </table>`;
            html += `</div>`;

            $(".body_modal").html(html);
            modal.show();
        })
    });
    $(document).on('click', '.unlock', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let keep = $(this).data("keep");

        post("home/unlock", {
            id,
            keep
        }).then(res => {
            loading("close");
            message(res.status, res.message);

            let html = '';
            res.data.forEach((e, i) => {
                html += `<tr>
                                <th scope="row">${(i+1)}</th>
                                <td>${time_php_to_js(e.tahun)}</td>
                                <td class="text-start">${e.masuk}</td>
                                <td class="text-end">${angka(e.keluar)}</td>
                                <td class="text-end">${angka(e.saldo)}</td>
                                <td class="text-center"><a class="unlock text-warning" data-keep="${e.keep}" data-id="${e.id}">${(e.keep==1?'<i class="fa-solid fa-lock text-secondary"></i>':'<i class="fa-solid fa-lock-open text-success"></i>')}</a></td>
                            </tr>`;
            })

            $(".tabel_unlock").html(html);
        })
    });
</script>
<?= $this->endSection() ?>