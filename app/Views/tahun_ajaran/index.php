<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-calendar-alt text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Tahun Ajaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Small Stat Cards Ringkasan -->
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= number_format($stats['total_ta'] ?? 0) ?></h3>
                    <p>Total Riwayat Tahun Ajaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= esc($stats['ta_aktif'] ?? 'Belum Diatur') ?></h3>
                    <p>Tahun Ajaran Aktif Saat Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <a href="<?= base_url('tahun-ajaran/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Tahun Ajaran Baru
                    </a>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <span class="badge badge-light border p-2"><i class="fas fa-info-circle text-info mr-1"></i> Hanya 1 Tahun Ajaran yang dapat diaktifkan.</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Tahun Ajaran</th>
                            <th width="140" class="text-center">Tahun Mulai</th>
                            <th width="140" class="text-center">Tahun Selesai</th>
                            <th width="140" class="text-center">Status</th>
                            <th width="220" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tahun_ajaran as $key => $item) : ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $key + 1 ?></td>
                                <td>
                                    <span class="font-weight-bold text-dark" style="font-size: 14px;">
                                        <i class="fas fa-calendar-check text-primary mr-2"></i><?= esc($item['nama_tahun_ajaran']) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= esc($item['tahun_mulai']) ?></td>
                                <td class="text-center"><?= esc($item['tahun_selesai']) ?></td>
                                <td class="text-center">
                                    <?php if ($item['status'] == 'aktif') : ?>
                                        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                                    <?php else : ?>
                                        <span class="badge badge-secondary px-3 py-2">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['status'] != 'aktif') : ?>
                                        <a href="<?= base_url('tahun-ajaran/set-active/' . $item['id']) ?>" class="btn btn-xs btn-success mr-1" title="Set Sebagai TA Aktif">
                                            <i class="fas fa-toggle-on mr-1"></i> Aktifkan
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('tahun-ajaran/' . $item['id'] . '/edit') ?>" class="btn btn-xs btn-warning mr-1" title="Edit Data">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= base_url('tahun-ajaran/' . $item['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-xs btn-danger btn-delete-swal" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tahun_ajaran)) : ?>
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada data tahun ajaran.</td></tr>
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
                text: "Data tahun ajaran ini akan dihapus secara permanen!",
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
