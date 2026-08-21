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
    <!-- Form Filter -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Pilih Tahun Ajaran & Kelas</h3>
        </div>
        <form method="get" action="<?= base_url('manajemen-kelas') ?>">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-control" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($tahun_ajaran as $ta) : ?>
                                    <option value="<?= $ta['id'] ?>" <?= ($selectedTahunId == $ta['id']) ? 'selected' : '' ?>>
                                        <?= esc($ta['nama_tahun_ajaran']) ?> <?= ($ta['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k) : ?>
                                    <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>>
                                        <?= esc($k['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group" style="width: 100%;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Tampilkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Tampilkan jika filter sudah dipilih -->
    <?php if ($selectedTahunId && $selectedKelasId) : ?>
    <div class="row">
        <!-- Kolom Siswa di dalam Kelas -->
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Siswa di Kelas Ini</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 400px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswaDiKelas as $sdk) : ?>
                            <tr>
                                <td><?= esc($sdk['nis']) ?></td>
                                <td><?= esc($sdk['nama_lengkap']) ?></td>
                                <td>
                                    <a href="<?= base_url('manajemen-kelas/unassign/' . $sdk['id']) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Keluarkan siswa ini dari kelas?')">
                                        <i class="fas fa-arrow-right"></i> Keluarkan
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($siswaDiKelas)) : ?>
                                <tr><td colspan="3" class="text-center">Belum ada siswa di kelas ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Siswa di Luar Kelas -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Siswa Belum Ditempatkan (Aktif)</h3>
                </div>
                <form action="<?= base_url('manajemen-kelas/assign') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" value="<?= $selectedKelasId ?>">
                    <input type="hidden" name="tahun_ajaran_id" value="<?= $selectedTahunId ?>">
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkAll"></th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($siswaLuarKelas as $slk) : ?>
                                <tr>
                                    <td><input type="checkbox" name="siswa_ids[]" value="<?= $slk['id'] ?>"></td>
                                    <td><?= esc($slk['nis']) ?></td>
                                    <td><?= esc($slk['nama_lengkap']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($siswaLuarKelas)) : ?>
                                    <tr><td colspan="3" class="text-center">Semua siswa aktif sudah ditempatkan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Masukkan siswa terpilih ke kelas ini?')">
                            <i class="fas fa-arrow-left"></i> Masukkan ke Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('checkAll').addEventListener('click', function(e) {
    document.querySelectorAll('input[name="siswa_ids[]"]').forEach(function(checkbox) {
        checkbox.checked = e.target.checked;
    });
});
</script>
<?= $this->endSection() ?>
