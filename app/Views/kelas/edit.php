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
            <h3 class="card-title">Form Edit Kelas</h3>
        </div>
        <form action="<?= base_url('kelas/' . $kelas['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_kelas">Nama Kelas</label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_kelas')) ? 'is-invalid' : '' ?>" id="nama_kelas" name="nama_kelas" value="<?= old('nama_kelas', $kelas['nama_kelas']) ?>" placeholder="Contoh: 10-A, 11-IPS-2">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_kelas') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tingkat">Tingkat</label>
                    <input type="number" class="form-control <?= ($validation->hasError('tingkat')) ? 'is-invalid' : '' ?>" id="tingkat" name="tingkat" value="<?= old('tingkat', $kelas['tingkat']) ?>" placeholder="Contoh: 10, 11, 12">
                    <div class="invalid-feedback">
                        <?= $validation->getError('tingkat') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="wali_kelas_id">Wali Kelas</label>
                    <select class="form-control" name="wali_kelas_id" id="wali_kelas_id">
                        <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                        <?php foreach ($guru as $g) : ?>
                            <option value="<?= $g['id'] ?>" <?= (old('wali_kelas_id', $kelas['wali_kelas_id']) == $g['id']) ? 'selected' : '' ?>><?= esc($g['nama_lengkap']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="<?= base_url('kelas') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
