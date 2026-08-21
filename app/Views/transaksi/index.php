<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <button type="button" class="btn btn-primary mr-2" id="btn-tambah">
                    <i class="fas fa-plus"></i> Transaksi Tunggal
                </button>
                <a href="<?= base_url('transaksi/kolektif') ?>" class="btn btn-success">
                    <i class="fas fa-layer-group"></i> Input Setoran Kolektif (Per Kelas)
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter & Pagination Controls -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="" method="get" class="d-flex">
                        <select name="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="10" <?= ($perPage == '10') ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= ($perPage == '20') ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= ($perPage == '50') ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= ($perPage == '100') ? 'selected' : '' ?>>100</option>
                        </select>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari NIS/Nama/Kode..." value="<?= esc($search) ?>">
                        <button type="submit" class="btn btn-primary btn-sm ml-2"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaksi as $item) : ?>
                            <tr>
                                <td><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></td>
                                <td><?= esc($item['kode_transaksi']) ?></td>
                                <td><?= esc($item['nis']) ?></td>
                                <td><?= esc($item['nama_siswa']) ?></td>
                                <td>
                                    <?php if ($item['jenis_transaksi'] == 'setor') : ?>
                                        <span class="badge bg-success">Setor</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Tarik</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                <td><?= esc($item['nama_pengguna']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($transaksi)) : ?>
                            <tr><td colspan="7" class="text-center">Tidak ada data transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?= $pager->links('default', 'default_full') ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Transaksi -->
<div class="modal fade" id="transaksiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transaksi-form" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="siswa_id">Siswa</label>
                        <select name="siswa_id" id="siswa_id" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach($siswa as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nis']) ?> - <?= esc($s['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" id="error-siswa_id"></div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_transaksi">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-control" required>
                            <option value="setor">Setor</option>
                            <option value="tarik">Tarik</option>
                        </select>
                        <div class="invalid-feedback" id="error-jenis_transaksi"></div>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah (Rp)</label>
                        <input type="text" name="jumlah" id="jumlah" class="form-control" required>
                        <div class="invalid-feedback" id="error-jumlah"></div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script custom transaksi -->
<script>
$(document).ready(function() {
    // Tombol Tambah diklik
    $('#btn-tambah').on('click', function() {
        $('#modal-title').text('Tambah Transaksi');
        $('#transaksi-form')[0].reset();
        $('#id').val('');
        $('.is-invalid').removeClass('is-invalid');
        $('#transaksiModal').modal('show');
    });

    // Form disubmit
    $('#transaksi-form').on('submit', function(e) {
        e.preventDefault();
        
        const btnSave = $('#btn-save');
        btnSave.prop('disabled', true);
        btnSave.find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '<?= base_url('transaksi/save') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('.is-invalid').removeClass('is-invalid');
                if (response.success) {
                    $('#transaksiModal').modal('hide');
                    alert(response.message);
                    window.location.reload(); // Reload halaman untuk melihat data baru
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#error-' + key).text(value);
                        });
                    }
                    if(response.message){
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            },
            complete: function() {
                btnSave.prop('disabled', false);
                btnSave.find('.spinner-border').addClass('d-none');
            }
        });
    });

    // Format input jumlah sebagai mata uang
    $('#jumlah').on('keyup', function() {
        // Implementasi sederhana, bisa diganti dengan library seperti cleave.js
        let value = $(this).val().replace(/[^0-9]/g, '');
        if(value) {
            $(this).val(new Intl.NumberFormat('id-ID').format(value));
        }
    });
});
</script>
<?= $this->endSection() ?>
