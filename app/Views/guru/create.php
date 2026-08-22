<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-plus text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('guru') ?>">Data Guru</a></li>
                    <li class="breadcrumb-item active">Tambah Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Formulir Tambah Guru / Pengguna Baru</h3>
        </div>
        <form action="<?= base_url('guru') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="Masukkan Nama Lengkap Beserta Gelar">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_lengkap') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">Username Login <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username') ?>" placeholder="Masukkan Username Login">
                    <div class="invalid-feedback">
                        <?= $validation->getError('username') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password Login <span class="text-danger">*</span></label>
                    <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Masukkan Password (Min. 6 Karakter)">
                    <div class="invalid-feedback">
                        <?= $validation->getError('password') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <button type="submit" class="btn btn-primary mr-1"><i class="fas fa-save mr-1"></i> Simpan Data Guru</button>
                <a href="<?= base_url('guru') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
