<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Form Edit Tahun Ajaran</h3>
        </div>
        <form action="<?= base_url('tahun-ajaran/' . $tahun_ajaran['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="card-body">
                <div class="form-group">
                    <label for="tahun_mulai">Tahun Mulai</label>
                    <input type="number" class="form-control <?= ($validation->hasError('tahun_mulai')) ? 'is-invalid' : '' ?>" id="tahun_mulai" name="tahun_mulai" value="<?= old('tahun_mulai', $tahun_ajaran['tahun_mulai']) ?>" placeholder="Contoh: 2024">
                    <div class="invalid-feedback">
                        <?= $validation->getError('tahun_mulai') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun_selesai">Tahun Selesai</label>
                    <input type="number" class="form-control <?= ($validation->hasError('tahun_selesai')) ? 'is-invalid' : '' ?>" id="tahun_selesai" name="tahun_selesai" value="<?= old('tahun_selesai', $tahun_ajaran['tahun_selesai']) ?>" placeholder="Contoh: 2025">
                    <div class="invalid-feedback">
                        <?= $validation->getError('tahun_selesai') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" id="status">
                        <option value="tidak aktif" <?= old('status', $tahun_ajaran['status']) == 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        <option value="aktif" <?= old('status', $tahun_ajaran['status']) == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="<?= base_url('tahun-ajaran') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
