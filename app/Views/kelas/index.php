<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-school text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Data Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Small Stat Cards Ringkasan -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= number_format($stats['total_kelas'] ?? 0) ?></h3>
                    <p>Total Rombel / Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= number_format($stats['total_wali'] ?? 0) ?> / <?= number_format($stats['total_kelas'] ?? 0) ?></h3>
                    <p>Wali Kelas Terisi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= number_format($stats['total_siswa'] ?? 0) ?></h3>
                    <p>Total Siswa Terdaftar (<?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Aktif') ?>)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <a href="<?= base_url('kelas/new?tahun_ajaran_id=' . $selectedTahunId) ?>" class="btn btn-primary mr-2">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Kelas Baru
                    </a>
                    <a href="<?= base_url('manajemen-kelas?tahun_ajaran_id=' . $selectedTahunId) ?>" class="btn btn-purple">
                        <i class="fas fa-random mr-1"></i> Penempatan & Kenaikan Kelas
                    </a>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <span class="badge badge-light border p-2"><i class="fas fa-calendar-alt text-primary mr-1"></i> TA Aktif: <strong><?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Aktif') ?></strong></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Bar Tahun Ajaran -->
            <form action="" method="get" class="mb-3">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="form-group mb-0 d-flex align-items-center">
                            <label for="tahun_ajaran_id" class="small font-weight-bold mr-2 mb-0 text-nowrap"><i class="fas fa-calendar-alt text-primary mr-1"></i> Filter Tahun Ajaran:</label>
                            <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control form-control-sm select2" onchange="this.form.submit()">
                                <?php foreach ($tahunAjaran as $t) : ?>
                                    <option value="<?= $t['id'] ?>" <?= ($selectedTahunId == $t['id']) ? 'selected' : '' ?>>
                                        <?= esc($t['nama_tahun_ajaran']) ?> <?= ($t['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="tableKelas" class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Kelas</th>
                            <th width="120" class="text-center">Tingkat</th>
                            <th>Wali Kelas</th>
                            <th width="140" class="text-center">Total Murid</th>
                            <th width="220" class="text-center text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kelas as $key => $item) : ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $key + 1 ?></td>
                                <td>
                                    <span class="badge badge-info p-2 font-weight-bold" style="font-size: 13px;">
                                        <i class="fas fa-school mr-1"></i><?= esc($item['nama_kelas']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold">Tingkat <?= esc($item['tingkat']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($item['nama_wali_kelas'])) : ?>
                                        <div class="d-flex align-items-center text-dark font-weight-bold">
                                            <i class="fas fa-user-tie text-success mr-2"></i>
                                            <?= esc($item['nama_wali_kelas']) ?>
                                        </div>
                                    <?php else : ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-exclamation-triangle mr-1"></i>Belum Diatur</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px;">
                                        <i class="fas fa-user-graduate mr-1"></i><?= number_format($item['total_siswa'] ?? 0) ?> Siswa
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('manajemen-kelas?tahun_ajaran_id=' . $selectedTahunId . '&kelas_id=' . $item['id']) ?>" class="btn btn-info" title="Atur Siswa di Kelas Ini">
                                            <i class="fas fa-user-cog mr-1"></i> Kelola
                                        </a>
                                        <a href="<?= base_url('kelas/' . $item['id'] . '/edit') ?>" class="btn btn-warning" title="Edit Data Kelas">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <form action="<?= base_url('kelas/' . $item['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" class="btn btn-danger btn-delete-swal" title="Hapus Data Kelas" style="border-top-left-radius:0; border-bottom-left-radius:0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kelas)) : ?>
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada data kelas yang terdaftar pada Tahun Ajaran ini. Silakan buat kelas baru untuk Tahun Ajaran ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-swal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data kelas ini akan dihapus secara permanen!",
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
});
</script>
<?= $this->endSection() ?>
