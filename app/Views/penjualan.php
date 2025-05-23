<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6 style="color: <?= tema('link_secondary'); ?>;"><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>

<button class="btn btn-sm btn-light my-3 transaksi"><i class="fa-solid fa-cash-register"></i> TRANSAKSI</b></button>
<button class="btn btn-sm btn-success my-3 body_total"></button>

<!-- Modal -->
<div class="modal fade" id="modal_add" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg_main">
            <div class="header text-center mt-5">
                <a href="" role="button" data-bs-dismiss="modal" class="text-danger fs-4"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="modal-body modal-fullscreen">
                <div class="container">
                    <form action="<?= base_url(menu()['controller']); ?>/add" method="post">

                        <div class="mb-3">
                            <label style="font-size: 12px;">Barang</label>
                            <input placeholder="Barang" type="text" name="barang" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Qty</label>
                            <input placeholder="Qty" type="text" name="qty" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input placeholder="Harga" type="text" name="harga" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-sm link_secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $total = 0; ?>
<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <table class="table table-sm table-bordered bg_main text_main" style="font-size: 14px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Nota</th>
                <th class="text-center">Barang</th>
                <th class="text-center">Total</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <?php $total += $i['total']; ?>
                <tr>
                    <td class="<?= ($i['ket'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><?= $k + 1; ?></td>
                    <td class="<?= ($i['ket'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><?= date('d', $i['tgl']); ?></td>
                    <?php if ($i['ket'] == "Hutang"): ?>
                        <td class="bg-danger bg-opacity-10 text-center"><a data-no_nota="<?= $i['no_nota']; ?>" class="text_main btn_lunas" href=""><?= $i['no_nota']; ?></a></td>
                    <?php else: ?>
                        <td class="text-center"><?= $i['no_nota']; ?></td>
                    <?php endif; ?>
                    <td class="<?= ($i['ket'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?>"><?= $i['barang']; ?></td>
                    <td class="<?= ($i['ket'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-end"><?= angka($i['total']); ?></td>
                    <td class="<?= ($i['ket'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-center">
                        <a data-id="<?= $i['id']; ?>" href="" class="text_main btn_detail"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        <?php if (user()['role'] == "Advisor"): ?>
                            | <a data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" href="" class="text_main btn_confirm btn_confirm_<?= $i['id']; ?>"><i class="fa-solid fa-circle-xmark text-danger"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php endif; ?>

<script>
    $(".body_total").text("<?= angka($total); ?>");
    let data = <?= json_encode($data); ?>;
    let data_selected = {};

    $(document).on("click", ".btn_detail", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        let val = [];

        data.forEach(e => {
            if (e.id == id) {
                val = e;
                stop();
            }
        });

        let html = `<div class="container">`;
        <?php if (user()['role'] == "Advisor"): ?>
            html += `<form  method="post" action="<?= base_url(menu()['controller']); ?>/update">
            <input type="hidden" value="${val.id}" name="id">
            <div class="body_user_id"></div>`;
        <?php endif; ?>
        html += `<div class="mb-3">
                            <label style="font-size: 12px;">No. Nota</label>
                            <input type="text" value="${val.no_nota}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Tgl</label>
                            <input type="text" value="${time_php_to_js(val.tgl)}" class="form-control form-control-sm" readonly>
                        </div>`;
        <?php if (user()['role'] == "Advisor"): ?>
            html += `<div class= mb-3" style="position: relative;">
                            <span class="text_main" style="font-size: small;">Pembeli</span>
                            <input name="pembeli" type="text" class="mb-2 form-control update_nama_pembeli nama_pembeli" data-user_id="${val.user_id}" value="${val.pembeli}" placeholder="Nama pembeli">
                            <div class="data_list_update_pembeli data_list"></div>
                        </div>`;
        <?php else: ?>
            html += `<div class="mb-3">
                            <label style="font-size: 12px;">Pembeli</label>
                            <input type="text" value="${(val.pembeli==''?'-':val.pembeli)}" class="form-control form-control-sm" readonly>
                        </div>`;
        <?php endif; ?>

        <?php if (user()['role'] == "Advisor"): ?>
            html += `<div class= mb-3" style="position: relative;">
                            <span class="text_main" style="font-size: small;">Barang</span>
                            <input name="barang" type="text" class="mb-2 form-control update_barang cari_barang" value="${val.barang}" placeholder="Barang">
                            <div class="data_list_update_barang data_list"></div>
                        </div>`;
        <?php else: ?>
            html += `<div class="mb-3">
                            <label style="font-size: 12px;">Barang</label>
                            <input type="text" value="${val.barang}" class="form-control form-control-sm" readonly>
                        </div>`;

        <?php endif; ?>
        html += `<div class="mb-3">
                            <label style="font-size: 12px;">Qty</label>
                            <input type="text" name="qty" value="${angka(val.qty)}" class="form-control angka update_qty form-control-sm" <?= (user()['role'] == "Advisor" ? "" : "readonly"); ?>>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input type="text" name="harga" value="${angka(val.harga)}" class="form-control update_harga form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Diskon</label>
                            <input type="text" name="diskon" value="${angka(val.diskon)}" class="form-control angka update_diskon form-control-sm" <?= (user()['role'] == "Advisor" ? "" : "readonly"); ?>>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Total</label>
                            <input type="text" name="total" value="${angka(val.total)}" class="form-control update_total form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Metode Bayar</label>
                            <input type="text" value="${val.ket}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">User Id</label>
                            <input type="text" value="${angka(val.user_id)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Petugas</label>
                            <input type="text" value="${val.petugas}" class="form-control form-control-sm" readonly>
                        </div>`;
        <?php if (user()['role'] == "Advisor"): ?>
            html += `  <div class="d-grid">
                            <button type="submit" class="btn btn-sm link_secondary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                        </div></form>`;
        <?php endif; ?>
        html += `</div>`;

        popupButton.html(html);
    })


    $(document).on("click", ".transaksi", function(e) {
        e.preventDefault();

        post("penjualan/no_nota", {
            id: 0
        }).then(res => {
            if (res.status == "200") {
                let html = `<div class="container">
                <h6 class="no_nota">NO. NOTA: ${res.data}</h6>
                        <table id="penjualan" class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Diskon</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Act</th>
                                </tr>
                            </thead>
                            <tbody class="isi_tabel">
                                <tr>
                                    <td style="vertical-align:middle" data-colspan="3" data-empty_cols="0" class="cari_barang add_barang" contenteditable="true"></td>
                                    <td style="vertical-align:middle" class="text-end add_qty angka_text" contenteditable="true">0</td>
                                    <td style="vertical-align:middle" class="text-end add_diskon angka_text" contenteditable="true">0</td>
                                    <td style="vertical-align:middle" class="text-end add_harga">0</td>
                                    <td style="vertical-align:middle" class="text-center"><a class="add_transaksi fw-bold fs-5 text-success" href="">+</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="daftar_transaksi" class="mt-2"></div>
                    </div>`

                popupButton.html(html);

                setTimeout(() => {
                    $('#fullscreen').on('shown.bs.modal', function() {
                        let input = $('.cari_barang');
                        input.focus();
                    });
                }, 200);
            } else {
                message(res.status, res.message);
            }
        })
    })


    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let value = $(this).text();
        let colspan = $(this).data("colspan");
        let empty_cols = parseInt($(this).data("empty_cols"));
        let order = ($(this).hasClass("update_barang") ? "update" : "add");

        $(".remove_all").remove();

        post("penjualan/cari_barang", {
            value
        }).then(res => {
            if (res.status == "200") {
                if (order == "add") {
                    $(".remove_all").remove();
                    let html = '';
                    for (let i = 0; i < empty_cols; i++) {
                        html += '<td class="remove_all"></td>';
                    }
                    if (res.data.length == 0) {
                        html += '<td class="remove_all" colspan="' + colspan + '"><i class="fa-solid fa-triangle-exclamation"></i> Data tidak ditemukan</td>';
                    } else {
                        html += '<td class="remove_all" colspan="' + colspan + '">';
                        html += '<div class="border_main text_main search_list" style="position: absolute;background-color:<?= tema('link_main'); ?>">';
                        res.data.forEach((e, i) => {
                            html += '<div data-order="' + order + '" data-id="' + e.id + '" data-satuan="' + e.satuan + '" data-qty="' + e.qty + '" data-barang="' + e.barang + '" data-jual="' + e.harga + '" class="p-1 border-bottom border_main select_barang" style="cursor: pointer;">' + e.barang + ' || ' + angka(e.qty) + '</div>';
                        })
                        html += '</div>';
                        html += '</td>';

                    }
                    $("#penjualan tr:last").after(html);

                } else {
                    let html = "";
                    if (res.data.length == 0) {
                        html += '<div>Data tidak ditemukan!.</div>';
                    }
                    res.data.forEach(e => {
                        html += '<div data-order="' + order + '" data-qty="' + e.qty + '" data-harga="' + e.harga + '" data-barang="' + e.barang + '" class="select_barang">' + e.barang + '/' + e.qty + '</div>';
                    })

                    $(".data_list_update_barang").html(html);
                }

            } else {
                popup_confirm.message(res.status, res.message);
            }
        })


    });
    $(document).on('click', '.select_barang', function(e) {
        e.preventDefault();
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let qty = parseInt($(this).data("qty"));
        let jual = parseInt($(this).data("jual"));
        let satuan = $(this).data("satuan");
        let order = $(this).data("order");

        if (order == "add") {
            if (qty <= 0) {
                message('400', "Stok barang: " + angka(qty));
                return;
            }
            // data_selected['id'] = id;
            data_selected['id'] = id;
            data_selected['barang'] = barang;
            data_selected['qty'] = qty;
            data_selected['jual'] = jual;
            data_selected['satuan'] = satuan;
            $("#penjualan tr:last td:first").text(barang);
            $("#penjualan tr:last").find("td").eq(1).text(1);
            $("#penjualan tr:last").find("td").eq(2).text(0);
            $("#penjualan tr:last").find("td").eq(3).text(angka(data_selected.jual));
            $(".search_list").remove();

        } else {
            $(".update_barang").val(barang);
            let update_qty = parseInt(str_replace(".", "", $(".update_qty").val()));
            let update_diskon = parseInt(str_replace(".", "", $(".update_diskon").val()));
            let update_harga = parseInt($(this).data("harga"));

            $(".update_harga").val(angka(update_harga));
            $(".update_total").val(angka((update_harga * update_qty) - update_diskon));
            $(".data_list_update_barang").html("");
        }




    });

    $(document).on('keyup', '.add_qty', function(e) {
        e.preventDefault();
        let qty = parseInt(str_replace(".", "", $(this).text()));
        let diskon = parseInt(str_replace(".", "", $(".add_diskon").text()));


        if (data_selected.qty == undefined) {
            message("400", "Barang belum dipilih!.");
            $(this).text(0);
            return;
        }



        if (qty == 0 || qty == "") {
            message("400", "Minimal pembelian: 1 Barang");
            $(this).text(1);
            $(".add_harga").text(angka(data_selected.jual - diskon));
            return;
        }


        if (qty > data_selected.qty) {
            message("400", "Stok barang: " + data_selected.qty);
            $(this).text(angka(data_selected.qty));
            $("add_harga").text(angka((data_selected.jual * data_selected.qty) - diskon));
            return;
        }


        $(".add_harga").text(angka((data_selected.jual * qty) - diskon));
    });

    $(document).on('keyup', '.add_diskon', function(e) {
        e.preventDefault();
        let diskon = parseInt(str_replace(".", "", $(this).text()));
        let val_qty = $(".isi_tabel tr").eq(0).find("td").eq(1).text();
        qty = parseInt(str_replace(".", "", val_qty));

        if (data_selected.jual == undefined) {
            message("400", "Barang belum dipilih!.");
            $(this).text(0);
            return;
        }

        let harga = data_selected.jual * qty;
        if (diskon > harga) {
            message("400", "Diskon maksimal: " + angka(harga));
            $(this).text(angka(harga));
            $(".add_harga").text(0);
            return;
        }

        if (diskon > 0) {
            $(".add_harga").text(angka(harga - diskon));
        } else {
            $(".add_harga").text(angka(harga));
        }
        data_selected['diskon'] = diskon;

    });
    let daftar_transaksi = [];
    const isi_transaksi_template = (order = "") => {
        let html = "";

        let total = 0;
        daftar_transaksi.forEach((e, i) => {
            total += parseInt(e.total);
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td>' + e.barang + '</td>';
            html += '<td class="text-end">' + angka(e.qty) + '</td>';
            html += '<td class="text-end">' + angka(e.diskon) + '</td>';
            html += '<td class="text-end">' + angka(e.total) + '</td>';
            if (order == "") {
                html += '<td class="text-center"><a href="" class="del_list text-danger" data-index="' + i + '"><i class="fa-solid fa-circle-xmark"></i></a></td>';
            }
            html += '</tr>';
        })
        html += '<tr>';
        html += '<th colspan="4" class="text-center">TOTAL</th>';
        html += '<th colspan="2" class="text-end">' + (angka(total)) + '</th>';
        html += '</tr>';
        if (order == "") {
            $(".body_btn_pembayaran").html('<button data-total="' + total + '" class="btn_pembayaran btn btn-sm btn-light"><i class="fa-solid fa-cash-register"></i> PEMBAYARAN</button>')

        }
        return html;
    }

    $(document).on('click', '.add_transaksi', function(e) {

        e.preventDefault();
        let barang = $(".add_barang").text();
        let qty = parseInt(str_replace(".", "", $(".add_qty").text()));
        let diskon = parseInt(str_replace(".", "", $(".add_diskon").text()));
        let total = parseInt(str_replace(".", "", $(".add_harga").text()));
        let harga = parseInt(data_selected.jual);
        let satuan = data_selected.satuan;
        let id = data_selected.id;

        if (barang == "") {
            message("400", "Barang kosong!.");
            return;
        }
        if (qty == "0") {
            message("400", "Qty nol!.");
            return;
        }
        if (harga == "0") {
            message("400", "Harga nol!.");
            return;
        }

        daftar_transaksi.push({
            barang,
            id,
            qty,
            diskon,
            harga,
            satuan,
            total
        });

        if ($("#daftar_transaksi").is(":empty")) {
            let html = "";
            html += `
                        <h6 style="font-size:10px">DAFTAR TRANSAKSI</h6>
                        <table id="rincian_transaksi" class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th text-center>#</th>
                                    <th text-center>Barang</th>
                                    <th text-center>Qty</th>
                                    <th text-center>Diskon</th>
                                    <th text-center>Harga</th>
                                    <th text-center>Act</th>
                                </tr>
                            </thead>
                            <tbody class="isi_transaksi">
                                <tr>
                                    <td>1</td>
                                    <td>${barang}</td>
                                    <td class="text-end">${angka(qty)}</td>
                                    <td class="text-end">${angka(diskon)}</td>
                                    <td class="text-end">${angka(total)}</td>
                                    <td class="text-center"><a href="" class="del_list text-danger text-center" data-index="0"><i class="fa-solid fa-circle-xmark"></i></a></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-center">TOTAL</th>
                                    <th colspan="2" class="text-end">${angka(total)}</th>
                                    </tr>
                        </tbody>
                        </table>
                        `;
            html += '<div class="d-grid mt-2 body_btn_pembayaran">';
            html += '<button data-total="' + total + '" class="btn_pembayaran btn btn-sm btn-light"><i class="fa-solid fa-cash-register"></i> TRANSAKSI</button>';
            html += '</div>';
            $("#daftar_transaksi").html(html);

        } else {

            let html = isi_transaksi_template();

            $(".isi_transaksi").html(html);
        }


        $(".search_list").remove();

        $(".add_barang").text("");
        $(".add_qty").text("0");
        $(".add_diskon").text("0");
        $(".add_harga").text("0");

    });


    $(document).on('click', '.del_list', function(e) {
        e.preventDefault();
        let index = parseInt($(this).data("index"));
        let data = [];

        daftar_transaksi.forEach((e, i) => {
            if (i !== index) {
                data.push(e);
            }
        })
        daftar_transaksi = data;
        let html = isi_transaksi_template();

        $(".isi_transaksi").html(html);
    });

    $(document).on('click', '.btn_pembayaran', function(e) {
        e.preventDefault();
        let myModal = document.getElementById("fullscreen");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.hide();

        let no_nota = $(".no_nota").text();
        let total = $(this).data("total");


        let html = "";
        html += `<div class="container border border-light rounded p-2">
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div class="fw-bold total_pembayaran">${angka(total)}</div>
                        </div>
                        <hr>
                        <div class="text-center mb-3" style="position: relative;">
                            <span class="text_main" style="font-size: small;">Pembeli</span>
                            <input type="text" class="mb-2 form-control nama_pembeli text-center" value="" placeholder="Nama pembeli">
                            <div class="data_list"></div>
                        </div>
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control uang_pembayaran text-center angka" value="${angka(total)}" placeholder="Uang pembayaran">
                        </div>
                        <div class="mb-3 text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Hutang" checked>
                                <label class="form-check-label">Hutang</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Cash">
                                <label class="form-check-label">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Barcode">
                                <label class="form-check-label">Barcode</label>
                            </div>
                        </div>
                    <div class="d-grid">
                        <button data-no_nota="${no_nota}" class="btn btn-sm btn-success btn_transaksi"><i class="fa-solid fa-wallet"></i> OK</button>
                    </div>`;

        html += '<div class="accordion accordion-flush mt-3" id="accordionFlushExample">';
        html += '<div class="accordion-item bg_main">';
        html += '<h2 class="accordion-header" id="flush-headingOne">';
        html += '<button style="font-size: small;" class="p-1 accordion-button collapsed bg_main text-light border border_main" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">';
        html += 'DETAIL';
        html += '</button>';
        html == '</h2>';
        html += '<div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">';
        html += `<table id="rincian_transaksi" class="table table-sm table-dark table-bordered" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th text-center>#</th>
                                    <th text-center>Barang</th>
                                    <th text-center>Qty</th>
                                    <th text-center>Diskon</th>
                                    <th text-center>Harga</th>
                                </tr>
                            </thead>
                            <tbody class="isi_transaksi">`;
        html += isi_transaksi_template("no");
        html += '</tbody>'
        html += '</table>';
        html += '</div>';
        html += '</div>';
        html += '</div>';


        html += '</div>';


        popupButton.html(html);

        setTimeout(() => {
            $('#fullscreen').on('shown.bs.modal', function() {
                let input = $('.nama_pembeli');
                input.focus();

                // Pindahkan kursor ke akhir teks di input
                let inputElement = input[0];
                let length = inputElement.value.length;
                inputElement.setSelectionRange(length, length);
            });
        }, 200);

    });
    $(document).on('click', '.btn_lunas', function(e) {
        e.preventDefault();

        let no_nota = $(this).data("no_nota");

        let val = [];
        let total = 0;
        data.forEach(e => {
            if (e.no_nota == no_nota) {
                total += parseInt(e.total);
                val.push(e);
            }
        });

        daftar_transaksi = val;
        let html = "";
        html += `<div class="container border border-light rounded p-2">
            <div class="input-group input-group-sm mb-2">
                 <span class="input-group-text bg_main border border_main">No. Nota</span>
                 <input type="text" class="form-control bg_main border border_main text_main" value="${val[0].no_nota}">
             </div>
             <div class="input-group input-group-sm mb-3">
                 <span class="input-group-text bg_main border border_main">Pembeli</span>
                 <input type="text" class="form-control bg_main border border_main text_main" value="${val[0].pembeli}">
             </div>
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div class="fw-bold total_pembayaran">${angka(total)}</div>
                        </div>
                        <hr>
                      
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control uang_pembayaran text-center angka" value="${angka(total)}" placeholder="Uang pembayaran">
                        </div>
                        <div class="mb-3 text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Cash" checked>
                                <label class="form-check-label">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Barcode">
                                <label class="form-check-label">Barcode</label>
                            </div>
                        </div>
                    <div class="d-grid">
                        <button data-no_nota="${no_nota}" data-order="Lunas" class="btn btn-sm btn-success btn_transaksi"><i class="fa-solid fa-wallet"></i> OK</button>
                    </div>`;

        html += '<div class="accordion accordion-flush mt-3" id="accordionFlushExample">';
        html += '<div class="accordion-item bg_main">';
        html += '<h2 class="accordion-header" id="flush-headingOne">';
        html += '<button style="font-size: small;" class="p-1 accordion-button collapsed bg_main text-light border border_main" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">';
        html += 'DETAIL';
        html += '</button>';
        html == '</h2>';
        html += '<div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">';
        html += `<table id="rincian_transaksi" class="table table-sm table-dark table-bordered" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th text-center>#</th>
                                    <th text-center>Barang</th>
                                    <th text-center>Qty</th>
                                    <th text-center>Diskon</th>
                                    <th text-center>Harga</th>
                                </tr>
                            </thead>
                            <tbody class="isi_transaksi">`;
        html += isi_transaksi_template("no");
        html += '</tbody>'
        html += '</table>';
        html += '</div>';
        html += '</div>';
        html += '</div>';


        html += '</div>';


        popupButton.html(html);

        setTimeout(() => {
            $('#fullscreen').on('shown.bs.modal', function() {
                let input = $('.nama_pembeli');
                input.focus();

                // Pindahkan kursor ke akhir teks di input
                let inputElement = input[0];
                let length = inputElement.value.length;
                inputElement.setSelectionRange(length, length);
            });
        }, 200);

    });

    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let uang_pembayaran = parseInt(str_replace(".", "", $(".uang_pembayaran").val()));
        let total_pembayaran = parseInt(str_replace(".", "", $(".total_pembayaran").text()));
        let no_nota = $(this).data('no_nota');
        let order = $(this).data('order');
        let nama_pembeli = $(".nama_pembeli").val();
        let user_id = $(".btn_transaksi").data("id");
        let ket = $('input[name="metode_bayar"]:checked').val();

        if (nama_pembeli == "") {
            message("400", "Pembeli kosong!.");
            return;
        }

        if (uang_pembayaran < total_pembayaran) {
            message("400", "Uang kurang!.");
            $(this).val(angka(total_pembayaran));

            return;
        }

        post("penjualan/transaksi", {
            uang_pembayaran,
            nama_pembeli,
            no_nota,
            daftar_transaksi,
            user_id,
            ket,
            order
        }).then(res => {
            if (res.status == "200") {
                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();

                let html = "";
                html += `<div class="container border border-light rounded p-2">
                                <div class="text-center mb-3">
                                    <span class="text_main" style="font-size: small;">UANG KEMBALIAN</span>
                                    <div class="fw-bold total_pembayaran">${angka(res.data)}</div>
                                </div>
                                <hr>`;
                if (res.data2.length > 0) {
                    html += '<div class="bg-opacity-25 bg-danger border border-danger mb-2" px-5 pb-1 rounded text-center" style="font-size: medium;">GAGAL: ' + res.data + '</div>';
                }
                if (ket !== "Hutang") {
                    html += `<div class="d-grid">
                                        <a target="_blank" href="${res.data3}" class="btn btn-sm btn-success"><i class="fa-regular fa-file-pdf"></i> Cetak Nota</a>
                                        </div>
                                </div>`;
                }

                popupButton.html(html);
            } else {
                message(res.message);
            }

            const modal = document.getElementById('fullscreen');
            modal.addEventListener('hidden.bs.modal', function() {
                // Reload the page when the modal is hidden
                location.reload();
            });
        })

    });

    $(document).on('keyup', '.nama_pembeli', function(e) {
        e.preventDefault();
        let val = $(this).val();
        let order = ($(this).hasClass("update_nama_pembeli") ? "update" : "add");
        post("penjualan/user", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-order="' + order + '" data-id="' + e.id + '" class="select_user">' + e.nama + '</div>';
            })
            if (order == "add") {
                $(".data_list").html(html);
            } else {
                $(".data_list_update_pembeli").html(html);
            }
        })
    });

    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let id = $(this).data("id");
        let order = $(this).data("order");

        if (order == "add") {
            $(".nama_pembeli").val(nama);
            $(".btn_transaksi").attr("data-id", id);
            $(".data_list").html("");
        } else {
            $(".update_nama_pembeli").val(nama);
            $(".body_user_id").html('<input type="hidden" value="' + id + '" name="user_id">');
            $(".data_list_update_pembeli").html("");
        }
    });
    $(document).on("click", ".btn_confirm", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        popup_confirm.confirm("btn_confirm_" + id);
    })
    $(document).on("click", ".btn_delete", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let tabel = $(this).data("tabel");

        post("home/delete", {
            tabel,
            id
        }).then(res => {
            if (res.status == "200") {
                popup.message(res.status, res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                popup.message(res.status, res.message);
            }
        })
    })
</script>

<?= $this->endSection() ?>