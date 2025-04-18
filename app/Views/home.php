<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<div class="text-center mb-3" style="margin-top: 80px;">WELCOME <b><?= strtoupper(user()['nama']); ?></b></div>

<div class="d-flex justify-content-between bg_secondary p-2" style="border-radius:10px 10px 0px 0px">
    <div style="font-size: 10px;">
        <div><i class="fa-regular fa-file-lines"></i> LAPORAN KEUANGAN</div>
        <div style="font-weight: normal;" class="total_laporan"></div>
    </div>
    <div>
        <select style="font-size: small;" class="form-select form-select-sm get_laporan" data-tabel="laporan">
            <?php foreach (get_tahun() as $i) : ?>
                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
            <?php endforeach; ?>
            <option value="All">All</option>
        </select>
    </div>
</div>
<div style="font-weight: normal;font-size:x-small; margin-top:-5px" class="mb-1 div_data_tap_laporan"></div>
<div class="p-2 border_main" style="border-radius: 0px 0px 10px 10px;">
    <canvas id="chart_laporan" style="width:90%;"></canvas>
</div>

<div class="modal fade" id="detail_laporan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-body body_detail_laporan">

            </div>
        </div>
    </div>
</div>

<script>
    const content_table = (data, index) => {
        let html = '';

        html += '<div class="tabel_pendapatan">';
        html += '<table class="table table-dark table-striped table-bordered table-sm" style="font-size:10px">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align: center;" scope="row">#</th>';
        html += '<th style="text-align: center;" scope="row">Tgl</th>';
        html += '<th style="text-align: center;" scope="row">Barang</th>';
        html += '<th style="text-align: center;" scope="row">Total</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        let total = 0;
        let bulan = '';
        data.forEach((val, idx) => {
            if (val.bulan == index) {
                bulan = val.bln;
                total = val.total;
                val.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '</td>';
                    html += '<td style="text-align:center">' + e.tanggal + '</td>';
                    html += '<td>' + e.barang + '</td>';
                    html += '<td class="text-end">' + angka(e.total) + '</td>';
                    html += '</tr>';
                })
            }
        })

        html += '<tr>';
        html += '<th style="text-align:right" colspan="3">TOTAL</th>';
        html += '<th style="text-align:right">' + angka(total) + '</th>';
        html += '</td>';

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        let res = {
            total,
            bulan,
            html
        }
        return res;

    }

    const chart_html = (tahun) => {
        const bulans = <?= json_encode(bulan()); ?>;
        let valueY = [];
        bulans.forEach(e => {
            valueY.push(e.satuan);
        });

        post('home/statistik', {
            tahun
        }).then(res => {
            if (res.status == '200') {
                // total pemasukan

                let total_m = 0;

                res.data.forEach((val, idx) => {
                    total_m += val.total;

                    val.data.forEach(t => {
                        if (t.metode == "Tap") {
                            total_tap += parseInt(t[jml]);
                        }
                    });
                })

                // total pengeluaran
                let total_p = 0;
                res.data2.forEach(e => {
                    total_p += e.total;
                })

                $('.total_laporan').text(angka(total_m) + ' - ' + angka(total_p) + ' = ' + ((total_m - total_p) < 0 ? '-' : '') + angka((total_m - total_p).toString()));



                valueX = [];

                // res.data.forEach(e => {
                //     valueX.push(e.total);
                // })

                for (let i = 0; i < res.data.length; i++) {
                    valueX.push(res.data[i].total - res.data2[i].total);
                }

                new Chart("chart_laporan", {
                    type: "line",
                    data: {
                        labels: valueY,
                        datasets: [{
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "white",
                            borderColor: "grey",
                            data: valueX
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        onClick: (e, values) => {

                            let index = values[0]['_index'] + 1;

                            let body_table = content_table(res.data, index);

                            let html = '';
                            html += '<div class="mb-2" style="font-size:small">';
                            html += 'KEUANGAN BULAN ' + body_table.bulan.toUpperCase() + ' ' + tahun;
                            html += '</div>';
                            html += `<div class="d-flex gap-2 mb-1">
                                        <button style="font-size:small" class="btn btn-sm link_main detail_data" data-order="pengeluaran">Masuk</button>
                                        <button style="font-size:small" class="btn btn-sm link_main detail_data" data-order="pemasukan">Keluar</button>
                                        <a target="_blank" href="<?= base_url('guest/laporan/'); ?>${body_table.bulan.toLowerCase()}/${tahun}" style="font-size:small" class="btn btn-sm link_main"><i class="fa-regular fa-file-pdf"></i> Laporan</a>
                                    </div>
                                    <div class="body_detail border_main rounded p-3">
                                        <div class="content_table">
                                    ${body_table.html}
                                    </div>`;

                            $('.body_detail_laporan').html(html);
                            let myModal = document.getElementById('detail_laporan');
                            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                            modal.show();

                            let total_p = 0;
                            res.data2.forEach((val, idx) => {
                                if (val.bulan == index) {
                                    total_p = val.total;
                                }
                            })

                            $('.judul_laporan').text(angka(body_table.total) + ' - ' + angka(total_p) + ' = ' + ((body_table.total - total_p) < 0 ? '-' : '') + angka(body_table.total - total_p));

                            $(document).on('click', '.detail_data', function(e) {
                                e.preventDefault();
                                let order = $(this).data('order');
                                let elem = document.querySelectorAll('.detail_data');

                                elem.forEach(e => {
                                    e.classList.remove('active');
                                })
                                $(this).addClass('active');

                                let content = content_table((order == 'pemasukan' ? res.data : res.data2), index);

                                $('.content_table').html(content.html);
                            })

                            // let datasetIndex = activeEls[0].datasetIndex;
                            // let dataIndex = activeEls[0].index;
                            // let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                            // let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                            // let label = e.chart.data.labels[dataIndex];
                            // console.log("In click", datasetLabel, label, value);
                        }
                    }
                });
            } else {
                message("400", res.message);
            }
        })
    }

    $(document).on('change', '.get_laporan', function(e) {
        e.preventDefault();

        let tahun = $(this).val();

        chart_html(tahun);

    })

    chart_html('<?= date('Y'); ?>');
</script>
<?= $this->endSection() ?>