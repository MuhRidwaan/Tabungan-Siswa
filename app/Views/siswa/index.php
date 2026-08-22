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
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <div class="btn-group mb-2 mb-md-0" role="group">
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
            <div>
                <a href="<?= base_url('siswa/export?tahun_ajaran_id=' . $selectedTahunId . '&kelas_id=' . $selectedKelasId) ?>" class="btn btn-info">
                    <i class="fas fa-file-export mr-1"></i> Export Data Siswa (Excel)
                </a>
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
                                    <?= esc($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="q" class="small font-weight-bold">Cari Siswa</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="q" id="q" class="form-control" placeholder="Cari NIS / Nama Siswa..." value="<?= esc($search ?? '') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
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
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th width="100">NIS</th>
                            <th>Nama Lengkap</th>
                            <th width="140">Kelas Saat Ini</th>
                            <th width="60" class="text-center">L/P</th>
                            <th width="180" class="text-right">Saldo Tabungan</th>
                            <th width="90" class="text-center">Status</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $key => $item) : ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $key + 1 ?></td>
                                <td><span class="badge badge-secondary"><?= esc($item['nis']) ?></span></td>
                                <td class="font-weight-bold"><?= esc($item['nama_lengkap']) ?></td>
                                <td>
                                    <?php if (!empty($item['nama_kelas'])) : ?>
                                        <span class="badge badge-info"><i class="fas fa-school mr-1"></i><?= esc($item['nama_kelas']) ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-light text-muted">Belum Ditempatkan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= esc($item['jenis_kelamin']) ?></td>
                                <td class="text-right font-weight-bold text-success">
                                    Rp <?= number_format($item['saldo_akhir'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['status_siswa'] == 'aktif') : ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger"><?= ucfirst($item['status_siswa']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('siswa/' . $item['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= base_url('siswa/' . $item['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswa)) : ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data siswa yang sesuai filter.</td></tr>
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
        $('#tahun_ajaran_id, #kelas_id').on('change', function() {
            document.getElementById('formFilterSiswa').submit();
        });
    }
});
</script>

<?= $this->endSection() ?>
