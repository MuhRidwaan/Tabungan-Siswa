<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-cog text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php 
$fotoProfile = session()->get('foto_profil');
$avatarUrl = ($fotoProfile && file_exists(FCPATH . 'uploads/profile/' . $fotoProfile)) 
    ? base_url('uploads/profile/' . $fotoProfile) 
    : base_url('dist/img/user2-160x160.jpg');
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Widget Card -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <img class="profile-user-img img-fluid img-circle elevation-2" style="width: 120px; height: 120px; object-fit: cover;" src="<?= $avatarUrl ?>" alt="User profile picture">
                    <h3 class="profile-username font-weight-bold mt-2"><?= esc(session()->get('nama_lengkap') ?? $user->username) ?></h3>
                    <p class="text-muted mb-1"><?= esc($user->email ?? '') ?></p>
                    <span class="badge badge-success p-2 mb-3"><i class="fas fa-user-shield mr-1"></i> Akun Terverifikasi</span>

                    <ul class="list-group list-group-unbordered text-left mb-3">
                        <li class="list-group-item">
                            <b>Username</b> <a class="float-right text-dark"><?= esc($user->username) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>User ID</b> <a class="float-right text-dark">#<?= esc($user->id) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Status Account</b> <a class="float-right text-success font-weight-bold">Aktif</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Edit Profile Card -->
        <div class="col-md-8">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Form Edit Informasi Profil</h3>
                </div>
                <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="foto_profil"><i class="fas fa-camera mr-1"></i> Unggah Foto Profil Baru</label>
                            <input type="file" name="foto_profil" id="foto_profil" class="form-control-file" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control <?= ($validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" value="<?= old('nama_lengkap', session()->get('nama_lengkap') ?? $user->username) ?>" required>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama_lengkap') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="username">Username (Untuk Login)</label>
                                <input type="text" name="username" id="username" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : '' ?>" value="<?= old('username', $user->username) ?>" required>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('username') ?>
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="email">Alamat Email</label>
                                <input type="email" name="email" id="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : '' ?>" value="<?= old('email', $user->email ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('email') ?>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="text-info font-weight-bold"><i class="fas fa-lock mr-1"></i> Ubah Password (Opsional)</h5>
                        <p class="text-muted small">Kosongkan bidang password di bawah ini jika Anda tidak ingin mengubah password.</p>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" name="password" id="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : '' ?>" placeholder="Masukkan password baru...">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('password') ?>
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="password_confirm">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirm" id="password_confirm" class="form-control <?= ($validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" placeholder="Ketik ulang password baru...">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('password_confirm') ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        <button type="submit" class="btn text-white font-weight-bold shadow-sm" style="background-color: #0D9488; border-color: #0D9488;"><i class="fas fa-save mr-1"></i> Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
