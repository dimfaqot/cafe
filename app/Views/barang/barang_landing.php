<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<div class="input-group input-group-sm mb-2">
    <input type="text" class="form-control bg-dark text-light border-secondary cari_card" placeholder="Cari..." aria-label="Recipient's username" aria-describedby="button-addon2">
    <button class="btn btn-outline-light form_input" data-order="Add" type="button"><i class="fa-solid fa-circle-plus"></i> <?= menu()['menu']; ?></button>
</div>
<?php foreach ($data as $k => $i): ?>
    <div class="card text-bg-dark mb-3" data-menu="<?= $i['barang']; ?>">
        <div class="card-header <?= ($i['link'] !== "" ? "text-warning" : ''); ?>"><?= ($k + 1) . ". " . $i['barang']; ?> <?= ($i['link'] !== "" ? "(" . str_replace(",", "-", $i['barangs']) . ")" : ""); ?></div>
        <div class="card-body d-flex justify-content-between ps-4">
            <div class="text-secondary"><small><?= angka($i['harga']) . " [" . angka($i['qty']) . "] - " . $i['jenis'] . '/' . $i['tipe']; ?></small></div>
            <div>
                <button class="btn btn-sm btn-light me-2 form_input" data-order="Edit" data-id="<?= $i['id']; ?>">Edit</button>
                <?php if (user()['role'] == "Root"): ?>
                    <button class="btn btn-sm btn-danger delete" data-id="<?= $i['id']; ?>" data-message="Yakin hapus?" data-tabel="<?= menu()['tabel']; ?>" data-is_reload="reload">Delete</button>

                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    let role = "<?= user()['role']; ?>";
    let form_input = (order, id) => {
        let jenis = <?= json_encode(options("Kantin")); ?>;
        let qty = <?= json_encode(settings("qty")); ?>;


        let data = {};
        if (order == "Edit") {
            let val = <?= json_encode($data); ?>;
            val.forEach(e => {
                if (e.id == id) {
                    data = e;
                    return;
                }
            });
        }

        let html = `<div class="form-floating mb-3">
                        <select class="form-select bg-dark text-light border-secondary rounded" name="jenis">`;
        if (order == "Add") {
            html += `<option selected value="">Pilih Jenis</option>`;
        }
        jenis.forEach(e => {
            html += `<option ${(e.value==data.jenis?"selected":"")} value="${e.value}">${e.value}</option>`;

        })
        html += `</select>
                        <label class="text-secondary">Jenis</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="barang" ${(order=="Edit"?'value="'+data.barang+'"':"")} class="form-control bg-dark text-light" placeholder="Barang" ${(order=="Add"?"required":(role=="Root"?"required":"readonly"))}>
                        <label class="text-secondary">Barang</label>
                    </div>`;

        html += `<div class="form-floating mb-3">
                        <input type="text" name="qty" ${(order=="Edit"?'value="'+angka(data.qty)+'"':"")} class="form-control bg-dark text-light angka" placeholder="Qty" ${(qty.value == "open"?"required":"readonly")}>
                        <label class="text-secondary">Qty</label>
                    </div>`;

        if (order == "Edit") {
            html += `<input type="hidden" name="id" value="${data.id}">`;
        }
        html += `<div class="form-floating mb-3">
                        <input type="text" name="harga" ${(order=="Edit"?'value="'+angka(data.harga)+'"':"")} class="form-control bg-dark text-light angka" placeholder="Harga Jual" required>
                        <label class="text-secondary">Harga ${(data.jenis=="Kulakan"?"Beli":"Jual")}</label>
                    </div>`;
        html += `<div class="form-floating mb-3">
                        <input type="text" name="links" ${(order=="Edit"?'value="'+data.barangs+'"':"")} data-order="${order}" class="form-control bg-dark text-light link_barang" placeholder="Link" readonly>
                        <label class="text-secondary">Link</label>
                    </div>`;

        html += `<input type="hidden" name="link">`;

        html += ` <div class="my-3 border border-light rounded p-2 d-flex justify-content-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" name="tipe" type="checkbox" role="switch" ${(order=="Edit"?(data.tipe=="Mix"?"checked":""):"")}>
                            <label class="form-check-label">Mix</label>
                        </div>
                    </div>`;

        html += `<div class="d-grid">
                        <button type="submit" class="btn btn-outline-info">Simpan</button>
                    </div>`

        return html;
    }

    $(document).on('click', '.link_barang', function(e) {
        e.preventDefault();
        let link = $(this).val();
        let links = link.split(",");

        let order = $(this).data('order');
        let barangs = <?= json_encode($data); ?>;
        let html = build_html("Barang", "modal");
        html += `<div class="p-3"><div class="d-grid mb-2"><button class="btn btn-sm btn-success save_checked">Save</button></div>`;
        barangs.forEach(e => {
            if (e.link == "") {
                html += `
                        <div class="form-check">
                            <input class="form-check-input barang_checked" type="checkbox" data-id="${e.id}" value="${e.barang}" ${(links.includes(e.barang)?"checked":"")}>
                            <label class="form-check-label">
                                ${e.barang}
                            </label>
                        </div>              
                        `;

            }
        })
        html += `</div>`;
        $(".body_modal").html(html);
        modal.show();
    });

    $(document).on('click', '.save_checked', function(e) {
        e.preventDefault();
        let barangs = [];
        let ids = [];
        $('.barang_checked:checked').each(function() {
            barangs.push($(this).val());
            ids.push($(this).data("id"));
        });

        $('input[name="links"]').val(barangs.join(","));

        $('input[name="link"]').val(ids.join(","));
        modal.hide();
    });

    $(document).on('click', '.form_input', function(e) {
        e.preventDefault();
        loading();
        let order = $(this).data("order");
        let id = $(this).data("id");

        let html = build_html(order, "offcanvas");

        html += `<div class="container">
                        <form method="post" action="<?= base_url(menu()['controller'] . "/"); ?>${order.toLowerCase()}">`;
        html += form_input(order, id);
        html += `</form>
                    </div>`;

        $(".body_canvas").html(html);
        loading("close");
        canvas.show();
    });
</script>
<?= $this->endSection() ?>