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
                    <li class="breadcrumb-item"><a href="<?= base_url('tahun-ajaran') ?>">Tahun Ajaran</a></li>
                    <li class="breadcrumb-item active">Edit Data</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-warning card-outline">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Formulir Edit Tahun Ajaran</h3>
        </div>
        <form action="<?= base_url('tahun-ajaran/' . $tahun_ajaran['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="tahun_mulai">Tahun Mulai <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= ($validation->hasError('tahun_mulai')) ? 'is-invalid' : '' ?>" id="tahun_mulai" name="tahun_mulai" value="<?= old('tahun_mulai', $tahun_ajaran['tahun_mulai']) ?>" placeholder="Contoh: 2024">
                        <div class="invalid-feedback">
                            <?= $validation->getError('tahun_mulai') ?>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="tahun_selesai">Tahun Selesai <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= ($validation->hasError('tahun_selesai')) ? 'is-invalid' : '' ?>" id="tahun_selesai" name="tahun_selesai" value="<?= old('tahun_selesai', $tahun_ajaran['tahun_selesai']) ?>" placeholder="Contoh: 2025">
                        <div class="invalid-feedback">
                            <?= $validation->getError('tahun_selesai') ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status Pengaktifan</label>
                    <select class="form-control select2" name="status" id="status">
                        <option value="tidak aktif" <?= old('status', $tahun_ajaran['status']) == 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        <option value="aktif" <?= old('status', $tahun_ajaran['status']) == 'aktif' ? 'selected' : '' ?>>🟢 Aktif (Jadikan Tahun Ajaran Berjalan)</option>
                    </select>
                </div>
            </div>
            <div class="card-footer bg-light">
                <button type="submit" class="btn btn-warning mr-1"><i class="fas fa-save mr-1"></i> Update Tahun Ajaran</button>
                <a href="<?= base_url('tahun-ajaran') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
