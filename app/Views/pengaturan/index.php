<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Biaya Administrasi</h3>
                </div>
                <form action="<?= base_url('pengaturan/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="persen_admin_guru">Persentase untuk Guru (%)</label>
                            <input type="number" step="0.1" class="form-control <?= ($validation->hasError('persen_admin_guru')) ? 'is-invalid' : '' ?>" id="persen_admin_guru" name="persen_admin_guru" value="<?= old('persen_admin_guru', $pengaturan['persen_admin_guru'] ?? '0') ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('persen_admin_guru') ?>
                            </div>
                            <small class="form-text text-muted">Biaya admin yang dialokasikan untuk guru dari setiap setoran.</small>
                        </div>

                        <div class="form-group">
                            <label for="persen_admin_sekolah">Persentase untuk Sekolah (%)</label>
                            <input type="number" step="0.1" class="form-control <?= ($validation->hasError('persen_admin_sekolah')) ? 'is-invalid' : '' ?>" id="persen_admin_sekolah" name="persen_admin_sekolah" value="<?= old('persen_admin_sekolah', $pengaturan['persen_admin_sekolah'] ?? '0') ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('persen_admin_sekolah') ?>
                            </div>
                            <small class="form-text text-muted">Biaya admin yang dialokasikan untuk kas sekolah dari setiap setoran.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
