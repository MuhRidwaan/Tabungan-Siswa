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
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Siswa</h3>
        </div>
        <form action="<?= base_url('siswa') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label for="nis">NIS</label>
                    <input type="text" class="form-control <?= ($validation->hasError('nis')) ? 'is-invalid' : '' ?>" id="nis" name="nis" value="<?= old('nis') ?>" placeholder="Masukkan NIS">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nis') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="Masukkan Nama Lengkap">
                     <div class="invalid-feedback">
                        <?= $validation->getError('nama_lengkap') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="kelas_id">Penempatan Kelas <span class="badge badge-info">(Tahun Ajaran: <?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Aktif') ?>)</span></label>
                    <select name="kelas_id" id="kelas_id" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k) : ?>
                            <option value="<?= $k['id'] ?>" <?= (old('kelas_id') == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" <?= (old('jenis_kelamin') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= (old('jenis_kelamin') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                     <div class="invalid-feedback">
                        <?= $validation->getError('jenis_kelamin') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir') ?>">
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat') ?></textarea>
                </div>
                 <div class="form-group">
                    <label>Status Siswa</label>
                    <select name="status_siswa" class="form-control <?= ($validation->hasError('status_siswa')) ? 'is-invalid' : '' ?>">
                        <option value="aktif" <?= (old('status_siswa') == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                        <option value="lulus" <?= (old('status_siswa') == 'lulus') ? 'selected' : '' ?>>Lulus</option>
                        <option value="pindah" <?= (old('status_siswa') == 'pindah') ? 'selected' : '' ?>>Pindah</option>
                        <option value="nonaktif" <?= (old('status_siswa') == 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('siswa') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
