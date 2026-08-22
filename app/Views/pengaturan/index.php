<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-sliders-h text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Pengaturan Komisi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card card-primary card-outline">
                <div class="card-header py-3">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-percentage mr-2"></i>Alokasi Biaya Administrasi & Komisi</h3>
                </div>
                <form action="<?= base_url('pengaturan/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="persen_admin_guru"><i class="fas fa-chalkboard-teacher text-success mr-1"></i> Persentase Komisi untuk Guru (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control font-weight-bold <?= ($validation->hasError('persen_admin_guru')) ? 'is-invalid' : '' ?>" id="persen_admin_guru" name="persen_admin_guru" value="<?= old('persen_admin_guru', $pengaturan['persen_admin_guru'] ?? '0') ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold">%</span>
                                </div>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('persen_admin_guru') ?>
                                </div>
                            </div>
                            <small class="form-text text-muted">Biaya admin yang dialokasikan untuk guru/petugas dari setiap setoran.</small>
                        </div>

                        <div class="form-group">
                            <label for="persen_admin_sekolah"><i class="fas fa-building text-primary mr-1"></i> Persentase Alokasi untuk Kas Sekolah (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control font-weight-bold <?= ($validation->hasError('persen_admin_sekolah')) ? 'is-invalid' : '' ?>" id="persen_admin_sekolah" name="persen_admin_sekolah" value="<?= old('persen_admin_sekolah', $pengaturan['persen_admin_sekolah'] ?? '0') ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold">%</span>
                                </div>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('persen_admin_sekolah') ?>
                                </div>
                            </div>
                            <small class="form-text text-muted">Biaya admin yang dialokasikan untuk kas sekolah dari setiap setoran.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Pengaturan Komisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
