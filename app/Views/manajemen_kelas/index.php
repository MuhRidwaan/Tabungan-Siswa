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
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> Pilih Tahun Ajaran & Kelas</h3>
            <div>
                <button type="button" class="btn btn-sm btn-purple" data-toggle="modal" data-target="#modalPromote">
                    <i class="fas fa-level-up-alt mr-1"></i> Proses Kenaikan Kelas Massal
                </button>
            </div>
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
if (document.getElementById('checkAll')) {
    document.getElementById('checkAll').addEventListener('click', function(e) {
        document.querySelectorAll('input[name="siswa_ids[]"]').forEach(function(checkbox) {
            checkbox.checked = e.target.checked;
        });
    });
}
</script>

<!-- Modal Kenaikan Kelas Massal -->
<div class="modal fade" id="modalPromote" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-purple text-white">
                <h5 class="modal-title"><i class="fas fa-level-up-alt mr-2"></i>Proses Kenaikan Kelas & Moving Siswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('manajemen-kelas/promote') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Pilih Tahun Ajaran Asal & Kelas Asal. Seluruh siswa yang dicentang akan otomatis dipindahkan ke Tahun Ajaran Baru pada Kelas Tujuan (atau Lulus).
                    </div>

                    <div class="row">
                        <!-- Tahun Ajaran Lama -->
                        <div class="col-md-6 form-group">
                            <label>Tahun Ajaran Asal (Lama)</label>
                            <select name="tahun_ajaran_lama_id" class="form-control" required>
                                <option value="">-- Pilih Tahun Ajaran Asal --</option>
                                <?php foreach ($tahun_ajaran as $ta) : ?>
                                    <option value="<?= $ta['id'] ?>" <?= ($selectedTahunId == $ta['id']) ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Kelas Asal -->
                        <div class="col-md-6 form-group">
                            <label>Kelas Asal (Lama)</label>
                            <select name="kelas_asal_id" class="form-control" required>
                                <option value="">-- Pilih Kelas Asal --</option>
                                <?php foreach ($kelas as $k) : ?>
                                    <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row">
                        <!-- Tahun Ajaran Baru -->
                        <div class="col-md-6 form-group">
                            <label>Tahun Ajaran Baru (Tujuan)</label>
                            <select name="tahun_ajaran_baru_id" class="form-control" required>
                                <option value="">-- Pilih Tahun Ajaran Baru --</option>
                                <?php foreach ($tahun_ajaran as $ta) : ?>
                                    <option value="<?= $ta['id'] ?>" <?= ($ta['status'] == 'aktif') ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?> <?= ($ta['status'] == 'aktif') ? '(Aktif)' : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Kelas Tujuan -->
                        <div class="col-md-6 form-group">
                            <label>Kelas Tujuan (Naik Kelas / Lulus)</label>
                            <select name="kelas_tujuan_id" class="form-control" required>
                                <option value="">-- Pilih Kelas Tujuan --</option>
                                <?php foreach ($kelas as $k) : ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                                <option value="lulus">🎓 LULUSKAN SISWA (Tingkat Akhir)</option>
                            </select>
                        </div>
                    </div>

                    <?php if (!empty($siswaDiKelas)) : ?>
                        <label class="font-weight-bold mt-2">Daftar Siswa yang Akan Diproses:</label>
                        <div class="table-responsive" style="max-height: 250px;">
                            <table class="table table-sm table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="40" class="text-center"><input type="checkbox" id="checkAllPromote" checked></th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($siswaDiKelas as $s) : ?>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="siswa_ids[]" value="<?= $s['siswa_id'] ?>" checked></td>
                                            <td><?= esc($s['nis']) ?></td>
                                            <td><?= esc($s['nama_lengkap']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <script>
                            document.getElementById('checkAllPromote').addEventListener('click', function(e) {
                                document.querySelectorAll('input[name="siswa_ids[]"]').forEach(chk => chk.checked = e.target.checked);
                            });
                        </script>
                    <?php else: ?>
                        <div class="alert alert-warning py-2 small mt-2"><i class="fas fa-exclamation-triangle mr-1"></i> Pilih Kelas Asal pada filter di atas terlebih dahulu untuk menampilkan daftar siswa.</div>
                    <?php endif; ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-purple" onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas untuk siswa terpilih?')"><i class="fas fa-level-up-alt mr-1"></i> Eksekusi Kenaikan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
