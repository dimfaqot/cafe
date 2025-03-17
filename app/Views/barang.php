<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6 style="color: <?= tema('link_secondary'); ?>;"><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>
<button data-bs-toggle="modal" data-bs-target="#modal_add" class="btn btn-sm link_secondary my-3 add_data"><i class="fa-solid fa-circle-plus"></i> Tambah Data</b></button>

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

                        <div class="mb-3 text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Barang" checked>
                                <label class="form-check-label">Barang</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Inv">
                                <label class="form-check-label">Inv</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Donasi">
                                <label class="form-check-label">Donasi</label>
                            </div>
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
                <th class="text-center">Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td><?= $k + 1; ?></td>
                    <td><?= $i['barang']; ?></td>
                    <td class="text-end"><?= angka($i['qty']); ?></td>
                    <td class="text-end"><?= angka($i['harga']); ?></td>
                    <td class="text-center"><a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                    <!-- <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let options = <?= json_encode(options('Role')); ?>;

    $(document).on("click", ".btn_confirm", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        popup_confirm.confirm("btn_confirm_" + id);
    })
    $(document).on("click", ".btn_update", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        let val = [];

        data.forEach(e => {
            if (e.id == id) {
                val = e;
                stop();
            }
        });
        const popup1 = new Modal("button");
        let html = `<div class="container">
                        <form action="<?= base_url(menu()['controller']); ?>/update" method="post">
                        <input type="hidden" name="id" value="${val.id}">

                        <div class="mb-3">
                            <label style="font-size: 12px;">Barang</label>
                            <input placeholder="Barang" type="text" name="barang" value="${val.barang}" class="form-control form-control-sm" required>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-size: 12px;">Qty</label>
                            <input placeholder="Qty" type="text" name="qty" value="${angka(val.qty)}" class="form-control form-control-sm angka" required>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input placeholder="Harga" type="text" name="harga" value="${angka(val.harga)}" class="form-control form-control-sm angka" required>
                        </div>
                        <div class="mb-3 text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Barang" ${(val.kategori=="Barang"?"checked":"")}>
                                <label class="form-check-label">Barang</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Inv" ${(val.kategori=="Inv"?"checked":"")}>
                                <label class="form-check-label">Inv</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="kategori" type="radio" value="Donasi" ${(val.kategori=="Donasi"?"checked":"")}>
                                <label class="form-check-label">Donasi</label>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-sm link_secondary"><i class="fa-solid fa-square-pen"></i> Update</button>
                        </div>
                    </form></div>`;

        popup1.html(html);
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
                message(res.status, res.message);
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