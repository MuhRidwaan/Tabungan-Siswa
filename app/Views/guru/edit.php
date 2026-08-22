<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit text-warning mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('guru') ?>">Data Guru</a></li>
                    <li class="breadcrumb-item active">Edit Data</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-warning card-outline">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Formulir Edit Data Guru / Pengguna</h3>
        </div>
        <form action="<?= base_url('guru/' . $guru['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $guru['nama_lengkap']) ?>" placeholder="Masukkan Nama Lengkap Beserta Gelar">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_lengkap') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">Username Login <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username', $guru['username']) ?>" placeholder="Masukkan Username Login">
                    <div class="invalid-feedback">
                        <?= $validation->getError('username') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password Login (Opsional)</label>
                    <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small class="form-text text-muted">Isi kolom ini hanya jika ingin mengganti password akun guru ini.</small>
                    <div class="invalid-feedback">
                        <?= $validation->getError('password') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <button type="submit" class="btn btn-warning mr-1"><i class="fas fa-save mr-1"></i> Update Data Guru</button>
                <a href="<?= base_url('guru') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
