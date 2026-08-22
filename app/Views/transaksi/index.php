<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-receipt text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Riwayat Transaksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Small Stat Cards Ringkasan Kas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp <?= number_format($stats['total_kas'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Saldo Kas Tabungan Net</p>
                </div>
                <div class="icon">
                    <i class="fas fa-vault"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp <?= number_format($stats['total_setor'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Total Accumulation Setoran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-circle-down"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp <?= number_format($stats['total_tarik'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Total Accumulation Penarikan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-circle-up"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= number_format($stats['total_transaksi'] ?? 0) ?></h3>
                    <p>Total Record Transaksi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary mr-1 mb-1" id="btn-tambah" data-toggle="modal" data-target="#transaksiModal">
                        <i class="fas fa-plus-circle mr-1"></i> Transaksi Instan
                    </button>
                    <a href="<?= base_url('transaksi/kolektif') ?>" class="btn btn-success mr-1 mb-1">
                        <i class="fas fa-layer-group mr-1"></i> Setor/Tarik Kolektif (Per Kelas)
                    </a>
                    <a href="<?= base_url('transaksi/multi-tanggal') ?>" class="btn btn-info mb-1">
                        <i class="fas fa-calendar-alt mr-1"></i> Setor Multi-Tanggal (Per Siswa)
                    </a>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <span class="badge badge-light border p-2"><i class="fas fa-clock text-primary mr-1"></i> Update Real-time</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <!-- Filter Bar -->
            <form action="" method="get" class="mb-4" id="formFilterTransaksi">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label for="tahun_ajaran_id" class="small font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control form-control-sm select2" onchange="this.form.submit()">
                            <option value="semua" <?= ($selectedTahunId == 'semua') ? 'selected' : '' ?>>📋 Semua Tahun Ajaran</option>
                            <?php foreach ($tahunAjaran as $t) : ?>
                                <option value="<?= $t['id'] ?>" <?= ($selectedTahunId == $t['id']) ? 'selected' : '' ?>>
                                    <?= esc($t['nama_tahun_ajaran']) ?> <?= ($t['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="kelas_id" class="small font-weight-bold">Filter Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control form-control-sm select2" onchange="this.form.submit()">
                            <option value="semua" <?= ($selectedKelasId == 'semua') ? 'selected' : '' ?>>-- Semua Kelas --</option>
                            <?php foreach ($kelas as $k) : ?>
                                <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="q" class="small font-weight-bold">Cari Transaksi</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="q" id="q" class="form-control" placeholder="Cari Kode / NIS / Nama Siswa / Petugas..." value="<?= esc($search ?? '') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary" title="Cari Data"><i class="fas fa-search"></i></button>
                                <a href="<?= base_url('transaksi') ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="per_page" class="small font-weight-bold">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-control form-control-sm select2" onchange="this.form.submit()">
                            <option value="10" <?= ($perPage == '10') ? 'selected' : '' ?>>10 Data</option>
                            <option value="25" <?= ($perPage == '25') ? 'selected' : '' ?>>25 Data</option>
                            <option value="50" <?= ($perPage == '50') ? 'selected' : '' ?>>50 Data</option>
                            <option value="100" <?= ($perPage == '100') ? 'selected' : '' ?>>100 Data</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 data-table-server">
                    <thead class="bg-light">
                        <tr>
                            <th width="150" class="text-center">Waktu & Tanggal</th>
                            <th width="130">Kode Transaksi</th>
                            <th width="100">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="100" class="text-center">Jenis</th>
                            <th width="160" class="text-right">Nominal (Rp)</th>
                            <th width="140">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaksi as $item) : ?>
                            <tr>
                                <td class="text-center small font-weight-bold">
                                    <i class="far fa-calendar-alt text-info mr-1"></i><?= date('d/m/Y H:i', strtotime($item['tanggal_transaksi'] ?? $item['created_at'])) ?>
                                </td>
                                <td><span class="badge badge-secondary"><?= esc($item['kode_transaksi']) ?></span></td>
                                <td><span class="badge badge-light border"><?= esc($item['nis']) ?></span></td>
                                <td class="font-weight-bold">
                                    <a href="<?= base_url('siswa/' . $item['siswa_id'] . '/detail') ?>" class="text-dark" title="Lihat Buku Tabungan Siswa">
                                        <?= esc($item['nama_siswa']) ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['jenis_transaksi'] == 'setor') : ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down mr-1"></i>Setor</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up mr-1"></i>Tarik</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right font-weight-bold <?= ($item['jenis_transaksi'] == 'setor') ? 'text-success' : 'text-danger' ?>">
                                    <?= ($item['jenis_transaksi'] == 'setor') ? '+' : '-' ?> Rp <?= number_format($item['jumlah'], 0, ',', '.') ?>
                                </td>
                                <td class="small font-weight-bold"><i class="fas fa-user-circle text-primary mr-1"></i><?= esc($item['nama_pengguna']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($transaksi)) : ?>
                            <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada riwayat transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)) : ?>
                <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-muted small mb-2 mb-md-0"><i class="fas fa-info-circle mr-1"></i> Navigasi halaman riwayat transaksi.</span>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Transaksi Instan -->
<div class="modal fade" id="transaksiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title" id="modal-title"><i class="fas fa-cash-register mr-2"></i>Tambah Transaksi Instan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transaksi-form" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="siswa_id">Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" id="siswa_id" class="form-control select2" required style="width:100%;">
                            <option value="">-- Cari NIS / Nama Siswa --</option>
                            <?php foreach($siswa as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nis']) ?> - <?= esc($s['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" id="error-siswa_id"></div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_transaksi">Jenis Transaksi <span class="text-danger">*</span></label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-control select2" required style="width:100%;">
                            <option value="setor">🟢 Setor Tunai (Pemasukan)</option>
                            <option value="tarik">🔴 Tarik Tunai (Penarikan)</option>
                        </select>
                        <div class="invalid-feedback" id="error-jenis_transaksi"></div>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Nominal Transaksi (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="jumlah" id="jumlah" class="form-control form-control-lg text-right font-weight-bold" placeholder="0" required>
                        <div class="invalid-feedback" id="error-jumlah"></div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan / Catatan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Contoh: Setoran Mingguan"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox bg-light p-2 border rounded">
                            <input type="checkbox" class="custom-control-input" id="include_alokasi" name="include_alokasi" value="1">
                            <label class="custom-control-label font-weight-bold text-dark mb-0" for="include_alokasi">
                                <i class="fas fa-coins text-warning mr-1"></i> Catat Potongan Alokasi Bagi Hasil Kas (Sekolah & Guru)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">
                        <i class="fas fa-save mr-1"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '#btn-tambah', function(e) {
        e.preventDefault();
        $('#transaksi-form')[0].reset();
        $('#id').val('');
        $('.is-invalid').removeClass('is-invalid');
        if ($.fn.select2) {
            $('#siswa_id, #jenis_transaksi').val('').trigger('change');
        }
        $('#transaksiModal').modal('show');
    });

    $('#transaksiModal').on('shown.bs.modal', function() {
        if ($.fn.select2) {
            $('#siswa_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: '-- Cari NIS / Nama Siswa --',
                allowClear: true,
                dropdownParent: $('#transaksiModal')
            });
            $('#jenis_transaksi').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#transaksiModal')
            });
        }
    });

    $('#transaksi-form').on('submit', function(e) {
        e.preventDefault();
        
        const btnSave = $('#btn-save');
        btnSave.prop('disabled', true);
        btnSave.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('transaksi/save') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('.is-invalid').removeClass('is-invalid');
                if (response.success || response.status) {
                    $('#transaksiModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: response.message || 'Transaksi berhasil disimpan.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#error-' + key).text(value);
                        });
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan!',
                        text: response.message || 'Terjadi kesalahan validasi.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Terputus!',
                    text: 'Terjadi kesalahan jaringan.'
                });
            },
            complete: function() {
                btnSave.prop('disabled', false);
                btnSave.html('<i class="fas fa-save mr-1"></i> Simpan Transaksi');
            }
        });
    });

    $('#jumlah').on('keyup', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if(value) {
            $(this).val(new Intl.NumberFormat('id-ID').format(value));
        }
    });
});
</script>
<?= $this->endSection() ?>
