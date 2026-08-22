<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-sitemap text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Penempatan & Kenaikan Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Form Filter -->
    <div class="card card-outline card-primary">
        <div class="card-header border-0 py-3">
            <div class="row w-100 align-items-center m-0">
                <div class="col-md-8 p-0 mb-2 mb-md-0">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-2"></i> Filter Kelas & Tahun Ajaran</h3>
                </div>
                <div class="col-md-4 p-0 text-md-right text-left">
                    <button type="button" class="btn btn-purple shadow-sm" data-toggle="modal" data-target="#modalPromote">
                        <i class="fas fa-level-up-alt mr-1"></i> Kenaikan Kelas & Moving Siswa
                    </button>
                </div>
            </div>
        </div>
        <form method="get" action="<?= base_url('manajemen-kelas') ?>" id="formFilterManajemen">
            <div class="card-body bg-light">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-control select2" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($tahun_ajaran as $ta) : ?>
                                <option value="<?= $ta['id'] ?>" <?= ($selectedTahunId == $ta['id']) ? 'selected' : '' ?>>
                                    <?= esc($ta['nama_tahun_ajaran']) ?> <?= ($ta['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Target Kelas</label>
                        <select name="kelas_id" class="form-control select2" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k) : ?>
                                <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?> <?= !empty($k['nama_wali']) ? '(Wali: ' . esc($k['nama_wali']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tampilkan jika filter sudah dipilih -->
    <?php if ($selectedTahunId && $selectedKelasId) : ?>
    <div class="row">
        <!-- Kolom Siswa di dalam Kelas -->
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header py-3">
                    <h3 class="card-title font-weight-bold text-success"><i class="fas fa-user-graduate mr-2"></i>Siswa di Kelas Ini (<?= count($siswaDiKelas) ?> Siswa)</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 420px;">
                    <table class="table table-head-fixed text-nowrap table-hover">
                        <thead>
                            <tr class="bg-light">
                                <th width="100">NIS</th>
                                <th>Nama Lengkap</th>
                                <th width="110" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswaDiKelas as $sdk) : ?>
                            <tr>
                                <td><span class="badge badge-secondary"><?= esc($sdk['nis']) ?></span></td>
                                <td class="font-weight-bold"><?= esc($sdk['nama_lengkap']) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('manajemen-kelas/unassign/' . ($sdk['id'] ?? $sdk['riwayat_id'])) ?>" class="btn btn-xs btn-danger btn-unassign-swal" title="Keluarkan dari kelas">
                                        <i class="fas fa-arrow-right mr-1"></i> Keluarkan
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($siswaDiKelas)) : ?>
                                <tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada siswa di kelas ini pada Tahun Ajaran yang dipilih.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Siswa di Luar Kelas -->
        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header py-3">
                    <h3 class="card-title font-weight-bold text-info"><i class="fas fa-user-plus mr-2"></i>Siswa Belum Ditempatkan (<?= count($siswaLuarKelas) ?> Siswa)</h3>
                </div>
                <form action="<?= base_url('manajemen-kelas/assign') ?>" method="post" id="formAssignSiswa">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" value="<?= $selectedKelasId ?>">
                    <input type="hidden" name="tahun_ajaran_id" value="<?= $selectedTahunId ?>">
                    <div class="card-body table-responsive p-0" style="height: 420px;">
                        <table class="table table-head-fixed text-nowrap table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th width="40" class="text-center"><input type="checkbox" id="checkAll"></th>
                                    <th width="100">NIS</th>
                                    <th>Nama Lengkap</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($siswaLuarKelas as $slk) : ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="siswa_ids[]" value="<?= $slk['id'] ?>"></td>
                                    <td><span class="badge badge-secondary"><?= esc($slk['nis']) ?></span></td>
                                    <td class="font-weight-bold"><?= esc($slk['nama_lengkap']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($siswaLuarKelas)) : ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-check-circle text-success mr-1"></i> Semua siswa aktif sudah ditempatkan di kelas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-success font-weight-bold" id="btnAssign"><i class="fas fa-arrow-left mr-1"></i> Masukkan Siswa Terpilih ke Kelas Ini</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-light border py-3 text-center text-muted"><i class="fas fa-arrow-up mr-1"></i> Silakan pilih <strong>Tahun Ajaran</strong> dan <strong>Target Kelas</strong> di atas untuk mengelola penempatan siswa.</div>
    <?php endif; ?>
</div>

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
            <form action="<?= base_url('manajemen-kelas/promote') ?>" method="post" id="formPromoteSiswa">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Pilih Kelas Asal & Tahun Ajaran Lama. Siswa yang dicentang akan dipindahkan ke Tahun Ajaran Baru pada Kelas Tujuan (atau Lulus).
                    </div>

                    <div class="row">
                        <!-- Tahun Ajaran Lama -->
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Tahun Ajaran Asal (Lama)</label>
                            <select name="tahun_ajaran_lama_id" class="form-control select2" required>
                                <option value="">-- Pilih Tahun Ajaran Asal --</option>
                                <?php foreach ($tahun_ajaran as $ta) : ?>
                                    <option value="<?= $ta['id'] ?>" <?= ($selectedTahunId == $ta['id']) ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Kelas Asal -->
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kelas Asal (Lama)</label>
                            <select name="kelas_asal_id" class="form-control select2" required>
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
                            <label class="small font-weight-bold">Tahun Ajaran Baru (Tujuan)</label>
                            <select name="tahun_ajaran_baru_id" class="form-control select2" required>
                                <option value="">-- Pilih Tahun Ajaran Baru --</option>
                                <?php foreach ($tahun_ajaran as $ta) : ?>
                                    <option value="<?= $ta['id'] ?>" <?= ($ta['status'] == 'aktif') ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?> <?= ($ta['status'] == 'aktif') ? '(Aktif)' : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Kelas Tujuan -->
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kelas Tujuan (Naik Kelas / Lulus)</label>
                            <select name="kelas_tujuan_id" class="form-control select2" required>
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
                                    <tr class="bg-light">
                                        <th width="40" class="text-center"><input type="checkbox" id="checkAllPromote" checked></th>
                                        <th width="100">NIS</th>
                                        <th>Nama Siswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($siswaDiKelas as $s) : ?>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="siswa_ids[]" value="<?= $s['siswa_id'] ?>" checked></td>
                                            <td><span class="badge badge-secondary"><?= esc($s['nis']) ?></span></td>
                                            <td class="font-weight-bold"><?= esc($s['nama_lengkap']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2 small mt-2"><i class="fas fa-exclamation-triangle mr-1"></i> Pilih Kelas Asal pada filter layar terlebih dahulu untuk memunculkan daftar siswa.</div>
                    <?php endif; ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-purple font-weight-bold" id="btnSubmitPromote"><i class="fas fa-level-up-alt mr-1"></i> Eksekusi Kenaikan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('checkAll')) {
        document.getElementById('checkAll').addEventListener('click', function(e) {
            document.querySelectorAll('input[name="siswa_ids[]"]').forEach(function(chk) {
                chk.checked = e.target.checked;
            });
        });
    }

    if (document.getElementById('checkAllPromote')) {
        document.getElementById('checkAllPromote').addEventListener('click', function(e) {
            document.querySelectorAll('#formPromoteSiswa input[name="siswa_ids[]"]').forEach(function(chk) {
                chk.checked = e.target.checked;
            });
        });
    }

    // SweetAlert2 Unassign Confirmation
    document.querySelectorAll('.btn-unassign-swal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'Keluarkan Siswa?',
                text: "Siswa ini akan dikeluarkan dari daftar kelas saat ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-arrow-right mr-1"></i> Ya, Keluarkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
