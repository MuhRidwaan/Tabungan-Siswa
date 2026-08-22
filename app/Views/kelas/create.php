<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-plus-circle text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kelas') ?>">Data Kelas</a></li>
                    <li class="breadcrumb-item active">Tambah Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Formulir Tambah Kelas Baru</h3>
        </div>
        <form action="<?= base_url('kelas') ?>" method="post">
            <?= csrf_field() ?>
                <div class="form-group">
                    <label for="tahun_ajaran_id">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select class="form-control" name="tahun_ajaran_id" id="tahun_ajaran_id" required>
                        <?php foreach ($tahunAjaran as $ta) : ?>
                            <option value="<?= $ta['id'] ?>" <?= (old('tahun_ajaran_id', $selectedTahunId) == $ta['id']) ? 'selected' : '' ?>>
                                <?= esc($ta['nama_tahun_ajaran']) ?> <?= ($ta['status'] == 'aktif') ? '[Aktif]' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_kelas')) ? 'is-invalid' : '' ?>" id="nama_kelas" name="nama_kelas" value="<?= old('nama_kelas') ?>" placeholder="Contoh: Kelas 1, Kelas 2-A">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_kelas') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tingkat">Tingkat Kelas <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?= ($validation->hasError('tingkat')) ? 'is-invalid' : '' ?>" id="tingkat" name="tingkat" value="<?= old('tingkat') ?>" placeholder="Contoh: 1, 2, 3">
                    <small class="form-text text-muted">Gunakan angka urutan tingkat (misal: 1 untuk Kelas 1 SD / 7 untuk SMP).</small>
                    <div class="invalid-feedback">
                        <?= $validation->getError('tingkat') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="wali_kelas_id">Wali Kelas</label>
                    <select class="form-control select2" name="wali_kelas_id" id="wali_kelas_id">
                        <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                        <?php foreach ($guru as $g) : ?>
                            <option value="<?= $g['id'] ?>" <?= (old('wali_kelas_id') == $g['id']) ? 'selected' : '' ?>><?= esc($g['nama_lengkap']) ?> (<?= esc($g['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-footer bg-light">
                <button type="submit" class="btn btn-primary mr-1"><i class="fas fa-save mr-1"></i> Simpan Data Kelas</button>
                <a href="<?= base_url('kelas') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
