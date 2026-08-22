<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-tie text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Data Guru & Pengguna</li>
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
                    <h3><?= number_format($stats['total_pengguna'] ?? 0) ?></h3>
                    <p>Total User Sistem</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= number_format($stats['total_guru'] ?? 0) ?></h3>
                    <p>Guru / Wali Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= number_format($stats['total_admin'] ?? 0) ?></h3>
                    <p>Administrator Sistem</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <a href="<?= base_url('guru/new') ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Guru / Pengguna Baru
                    </a>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <span class="badge badge-light border p-2"><i class="fas fa-shield-alt text-primary mr-1"></i> Hak Akses Pengguna</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 data-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Lengkap</th>
                            <th width="180">Username</th>
                            <th width="130" class="text-center">Role / Hak Akses</th>
                            <th width="150" class="text-center text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guru as $key => $item) : ?>
                            <?php
                            $words = explode(' ', trim($item['nama_lengkap']));
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $avatarBg = ($item['role'] == 'admin') ? 'bg-danger' : 'bg-primary';
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $key + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle <?= $avatarBg ?> text-white font-weight-bold mr-2 text-center rounded-circle" style="width: 34px; height: 34px; line-height: 34px; font-size: 13px; flex-shrink:0;">
                                            <?= esc($initials) ?>
                                        </div>
                                        <span class="font-weight-bold text-dark"><?= esc($item['nama_lengkap']) ?></span>
                                    </div>
                                </td>
                                <td><span class="badge badge-secondary font-weight-bold"><i class="fas fa-user mr-1"></i><?= esc($item['username']) ?></span></td>
                                <td class="text-center">
                                    <?php if ($item['role'] == 'admin') : ?>
                                        <span class="badge badge-danger px-3 py-1"><i class="fas fa-user-shield mr-1"></i>Admin</span>
                                    <?php else : ?>
                                        <span class="badge badge-info px-3 py-1"><i class="fas fa-chalkboard-teacher mr-1"></i>Guru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('guru/' . $item['id'] . '/edit') ?>" class="btn btn-warning" title="Edit Data">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <form action="<?= base_url('guru/' . $item['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" class="btn btn-danger btn-delete-swal" title="Hapus Data" style="border-top-left-radius:0; border-bottom-left-radius:0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($guru)) : ?>
                            <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada data pengguna.</td></tr>
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
                text: "Data pengguna ini akan dihapus secara permanen!",
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
