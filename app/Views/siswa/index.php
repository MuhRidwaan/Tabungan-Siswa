<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-users text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Data Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Small Stat Cards Ringkasan -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= number_format($stats['total_siswa'] ?? 0) ?></h3>
                    <p>Total Siswa (Filtered)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp <?= number_format($stats['total_saldo'] ?? 0, 0, ',', '.') ?></h3>
                    <p>Total Tabungan Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= number_format($stats['total_laki'] ?? 0) ?></h3>
                    <p>Siswa Laki-laki (L)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-male"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?= number_format($stats['total_perempuan'] ?? 0) ?></h3>
                    <p>Siswa Perempuan (P)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-female"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card card-primary card-outline">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('siswa/new') ?>" class="btn btn-primary">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Siswa Baru
                        </a>
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalImport">
                            <i class="fas fa-file-excel mr-1"></i> Import Excel / CSV
                        </button>
                        <a href="<?= base_url('siswa/download-template') ?>" class="btn btn-outline-success">
                            <i class="fas fa-file-download mr-1"></i> Download Template Excel (.xls)
                        </a>
                    </div>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <a href="<?= base_url('siswa/export?tahun_ajaran_id=' . $selectedTahunId . '&kelas_id=' . $selectedKelasId . '&status_siswa=' . $statusFilter) ?>" class="btn btn-info">
                        <i class="fas fa-file-export mr-1"></i> Export Data Siswa (Excel)
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <!-- Filter Bar -->
            <form action="" method="get" class="mb-4" id="formFilterSiswa">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label for="tahun_ajaran_id" class="small font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control form-control-sm select2">
                            <?php foreach ($tahunAjaran as $t) : ?>
                                <option value="<?= $t['id'] ?>" <?= ($selectedTahunId == $t['id']) ? 'selected' : '' ?>>
                                    <?= esc($t['nama_tahun_ajaran']) ?> <?= ($t['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="kelas_id" class="small font-weight-bold">Filter Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control form-control-sm select2">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas as $k) : ?>
                                <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?> <?= !empty($k['nama_wali']) ? '(Wali: ' . esc($k['nama_wali']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="status_siswa" class="small font-weight-bold">Status Siswa</label>
                        <select name="status_siswa" id="status_siswa" class="form-control form-control-sm select2">
                            <option value="aktif" <?= ($statusFilter == 'aktif') ? 'selected' : '' ?>>🟢 Aktif (Default)</option>
                            <option value="lulus" <?= ($statusFilter == 'lulus') ? 'selected' : '' ?>>🎓 Lulus</option>
                            <option value="pindah" <?= ($statusFilter == 'pindah') ? 'selected' : '' ?>>🚨 Pindah</option>
                            <option value="nonaktif" <?= ($statusFilter == 'nonaktif') ? 'selected' : '' ?>>🔴 Nonaktif</option>
                            <option value="semua" <?= ($statusFilter == 'semua') ? 'selected' : '' ?>>📋 Semua Status</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="q" class="small font-weight-bold">Cari Siswa</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="q" id="q" class="form-control" placeholder="Cari NIS / Nama Siswa..." value="<?= esc($search ?? '') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary" title="Cari Data"><i class="fas fa-search"></i></button>
                                <a href="<?= base_url('siswa') ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="per_page" class="small font-weight-bold">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-control form-control-sm select2" onchange="this.form.submit()">
                            <option value="10" <?= ($perPage == '10') ? 'selected' : '' ?>>10 Data</option>
                            <option value="25" <?= ($perPage == '25') ? 'selected' : '' ?>>25 Data</option>
                            <option value="50" <?= ($perPage == '50') ? 'selected' : '' ?>>50 Data</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 data-table-server" id="tableSiswa">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th width="100">NIS</th>
                            <th>Nama Lengkap</th>
                            <th width="140">Kelas Saat Ini</th>
                            <th width="60" class="text-center">L/P</th>
                            <th width="180" class="text-right">Saldo Tabungan</th>
                            <th width="90" class="text-center">Status</th>
                            <th width="240" class="text-center text-nowrap">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $key => $item) : ?>
                            <?php
                            $words = explode(' ', trim($item['nama_lengkap']));
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $avatarBg = ($item['jenis_kelamin'] == 'P') ? 'bg-purple' : 'bg-primary';
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $key + 1 ?></td>
                                <td><span class="badge badge-secondary"><?= esc($item['nis']) ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle <?= $avatarBg ?> text-white font-weight-bold mr-2 text-center rounded-circle" style="width: 32px; height: 32px; line-height: 32px; font-size: 12px; flex-shrink: 0;">
                                            <?= esc($initials) ?>
                                        </div>
                                        <a href="<?= base_url('siswa/' . $item['id'] . '/detail') ?>" class="font-weight-bold text-dark" title="Lihat Detail Buku Tabungan">
                                            <?= esc($item['nama_lengkap']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($item['nama_kelas'])) : ?>
                                        <span class="badge badge-info"><i class="fas fa-school mr-1"></i><?= esc($item['nama_kelas']) ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-light text-muted">Belum Ditempatkan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($item['jenis_kelamin'] == 'P') ? 'badge-pink text-purple' : 'badge-light text-primary' ?> font-weight-bold"><?= esc($item['jenis_kelamin']) ?></span>
                                </td>
                                <td class="text-right font-weight-bold text-success" id="saldo_display_<?= $item['id'] ?>">
                                    Rp <?= number_format($item['saldo_akhir'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['status_siswa'] == 'aktif') : ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger"><?= ucfirst($item['status_siswa']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('siswa/' . $item['id'] . '/detail') ?>" class="btn btn-info" title="Lihat Detail Buku Tabungan Siswa">
                                            <i class="fas fa-wallet mr-1"></i> Detail
                                        </a>
                                        <button type="button" class="btn btn-success btn-quick-setor" 
                                                data-siswa-id="<?= $item['id'] ?>" 
                                                data-nama="<?= esc($item['nama_lengkap']) ?>" 
                                                data-nis="<?= esc($item['nis']) ?>" 
                                                data-saldo="<?= $item['saldo_akhir'] ?? 0 ?>"
                                                title="Setor/Tarik Cepat">
                                            <i class="fas fa-coins mr-1"></i> Setor/Tarik
                                        </button>
                                        <a href="<?= base_url('siswa/' . $item['id'] . '/edit') ?>" class="btn btn-warning" title="Edit Data Siswa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('siswa/' . $item['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" class="btn btn-danger btn-delete-swal" title="Hapus Data Siswa" style="border-top-left-radius:0; border-bottom-left-radius:0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswa)) : ?>
                            <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Tidak ada data siswa yang sesuai filter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)) : ?>
                <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-muted small mb-2 mb-md-0"><i class="fas fa-info-circle mr-1"></i> Gunakan navigasi halaman untuk berpindah data.</span>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Quick Setor/Tarik Direct Action -->
<div class="modal fade" id="modalQuickTrans" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title" id="quickTransTitle"><i class="fas fa-coins mr-2"></i>Transaksi Tabungan Instan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formQuickTrans">
                <?= csrf_field() ?>
                <input type="hidden" name="siswa_id" id="quick_siswa_id">
                <div class="modal-body">
                    <div class="alert alert-light border py-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Nama Siswa: <strong id="quick_nama_siswa">-</strong></span>
                            <span>NIS: <strong id="quick_nis_siswa">-</strong></span>
                        </div>
                        <div class="mt-1 text-success font-weight-bold">
                            Saldo Tabungan Saat Ini: <span id="quick_saldo_saat_ini">Rp 0</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quick_jenis"><i class="fas fa-exchange-alt mr-1"></i> Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="quick_jenis" class="form-control" required>
                            <option value="setor">🟢 Setor Tunai (Pemasukan)</option>
                            <option value="tarik">🔴 Tarik Tunai (Penarikan)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quick_jumlah"><i class="fas fa-money-bill-wave mr-1"></i> Nominal Transaksi (Rp)</label>
                        <input type="text" name="jumlah" id="quick_jumlah" class="form-control form-control-lg font-weight-bold text-right" placeholder="0" required>
                    </div>

                    <div class="form-group mb-0">
                        <label for="quick_ket"><i class="fas fa-comment-alt mr-1"></i> Catatan/Keterangan</label>
                        <input type="text" name="keterangan" id="quick_ket" class="form-control" placeholder="Contoh: Setoran Harian">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitQuick"><i class="fas fa-save mr-1"></i> Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel/CSV -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i>Import Data Siswa dari File Excel / CSV</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('siswa/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file_excel">Pilih File Excel (.xls, .csv)</label>
                        <input type="file" name="file_excel" id="file_excel" class="form-control-file" accept=".xls,.xlsx,.csv" required>
                    </div>
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle mr-1"></i> Gunakan format file Microsoft Excel yang telah disediakan pada tombol <strong>Download Template Excel (.xls)</strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload mr-1"></i> Upload & Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        $('#tahun_ajaran_id, #kelas_id, #status_siswa').on('change', function() {
            document.getElementById('formFilterSiswa').submit();
        });
    }

    // SweetAlert2 Delete Confirmation
    document.querySelectorAll('.btn-delete-swal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data siswa ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Format Rupiah Input
    const quickJumlah = document.getElementById('quick_jumlah');
    quickJumlah.addEventListener('keyup', function(e) {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) {
            this.value = parseInt(val, 10).toLocaleString('id-ID');
        } else {
            this.value = '';
        }
    });

    // Handle Quick Setor/Tarik Button Click
    document.querySelectorAll('.btn-quick-setor').forEach(btn => {
        btn.addEventListener('click', function() {
            const sid = this.dataset.siswaId;
            const nama = this.dataset.nama;
            const nis = this.dataset.nis;
            const saldo = parseFloat(this.dataset.saldo) || 0;

            document.getElementById('quick_siswa_id').value = sid;
            document.getElementById('quick_nama_siswa').innerText = nama;
            document.getElementById('quick_nis_siswa').innerText = nis;
            document.getElementById('quick_saldo_saat_ini').innerText = 'Rp ' + saldo.toLocaleString('id-ID');
            document.getElementById('quick_jumlah').value = '';
            document.getElementById('quick_ket').value = '';

            $('#modalQuickTrans').modal('show');
        });
    });

    // Submit Quick Transaction AJAX with SweetAlert2
    document.getElementById('formQuickTrans').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitQuick');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

        const formData = new FormData(this);

        fetch('<?= base_url('transaksi/save') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status || data.success) {
                $('#modalQuickTrans').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    text: data.message || 'Setoran/penarikan tabungan berhasil disimpan.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: data.message || 'Terjadi kesalahan sistem.'
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Transaksi';
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Terputus!',
                text: 'Terjadi kesalahan jaringan.'
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Transaksi';
        });
    });
});
</script>

<?= $this->endSection() ?>
