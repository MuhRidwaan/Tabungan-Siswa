<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-file-invoice text-primary mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Laporan & Cetak</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Form Filter -->
    <div class="card card-outline card-info">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-info mr-2"></i>Filter Generator Laporan</h3>
        </div>
        <form method="get" action="<?= base_url('laporan') ?>">
            <div class="card-body bg-light">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small font-weight-bold">Jenis Laporan</label>
                        <select name="jenis_laporan" id="jenis_laporan" class="form-control select2" required>
                            <option value="">-- Pilih Jenis Laporan --</option>
                            <option value="per_siswa" <?= ($jenisLaporan == 'per_siswa') ? 'selected' : '' ?>>📊 Laporan per Siswa</option>
                            <option value="per_kelas" <?= ($jenisLaporan == 'per_kelas') ? 'selected' : '' ?>>🏫 Rekapitulasi per Kelas</option>
                            <option value="pemasukan" <?= ($jenisLaporan == 'pemasukan') ? 'selected' : '' ?>>💰 Laporan Pemasukan Kas</option>
                        </select>
                    </div>
                    
                    <div id="filter-tahun-ajaran" class="col-lg-3 col-md-6 mb-2">
                        <label class="small font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control select2">
                            <?php if (isset($tahunAjaran)) : foreach ($tahunAjaran as $t) : ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($selectedTahunId) && $selectedTahunId == $t['id']) ? 'selected' : '' ?>>
                                    <?= esc($t['nama_tahun_ajaran']) ?> <?= ($t['status'] == 'aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <!-- Dynamic Filters -->
                    <div id="filter-per-siswa" class="col-lg-3 col-md-6 mb-2" style="display: none;">
                        <label class="small font-weight-bold">Pilih Siswa</label>
                        <select name="siswa_id" class="form-control select2">
                            <option value="">-- Cari NIS / Nama Siswa --</option>
                            <?php if(isset($listSiswa)): foreach ($listSiswa as $s) : ?>
                                <option value="<?= $s['id'] ?>" <?= (isset($_GET['siswa_id']) && $_GET['siswa_id'] == $s['id']) ? 'selected' : '' ?>><?= esc($s['nis']) ?> - <?= esc($s['nama_lengkap']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div id="filter-per-kelas" class="col-lg-3 col-md-6 mb-2" style="display: none;">
                        <label class="small font-weight-bold">Pilih Kelas</label>
                        <select name="kelas_id" class="form-control select2">
                            <option value="">-- Pilih Kelas --</option>
                            <?php if(isset($listKelas)): foreach ($listKelas as $k) : ?>
                                <option value="<?= $k['id'] ?>" <?= (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div id="filter-tanggal" class="col-lg-4 col-md-6 mb-2" style="display: none;">
                       <div class="row">
                           <div class="col-6">
                                <label class="small font-weight-bold">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
                           </div>
                           <div class="col-6">
                                <label class="small font-weight-bold">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
                           </div>
                       </div>
                    </div>
                    <!-- End Dynamic Filters -->

                    <div class="col-lg-2 col-md-12 mb-2">
                        <button type="submit" class="btn btn-info btn-block font-weight-bold">
                            <i class="fas fa-play mr-1"></i> Generate
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Display -->
    <?php if ($jenisLaporan && $reportData !== null) : ?>
        <div class="card card-primary card-outline">
            <div class="card-header border-0 py-3">
                <div class="row w-100 align-items-center m-0">
                    <div class="col-md-6 p-0 mb-2 mb-md-0">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-file-alt text-primary mr-2"></i> Hasil Laporan Tabungan</h3>
                    </div>
                    <div class="col-md-6 p-0 text-md-right text-left">
                        <a href="<?= base_url('laporan/export?' . ($_SERVER['QUERY_STRING'] ?? '')) ?>" class="btn btn-success mr-1">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel (.xls)
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print mr-1"></i> Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if ($jenisLaporan == 'per_siswa' && !empty($reportData)) : ?>
                    <?= $this->include('laporan/per_siswa') ?>
                <?php elseif ($jenisLaporan == 'per_kelas') : ?>
                    <?= $this->include('laporan/per_kelas') ?>
                <?php elseif ($jenisLaporan == 'pemasukan') : ?>
                    <?= $this->include('laporan/pemasukan') ?>
                <?php else: ?>
                    <div class="alert alert-warning py-3"><i class="fas fa-exclamation-triangle mr-1"></i> Silakan pilih filter yang sesuai atau tidak ada data untuk ditampilkan.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisLaporanSelect = document.getElementById('jenis_laporan');

    function toggleFilters() {
        const selectedValue = jenisLaporanSelect.value;
        document.getElementById('filter-per-siswa').style.display = 'none';
        document.getElementById('filter-per-kelas').style.display = 'none';
        document.getElementById('filter-tanggal').style.display = 'none';
        
        if (selectedValue === 'per_siswa') {
            document.getElementById('filter-per-siswa').style.display = 'block';
            document.getElementById('filter-tanggal').style.display = 'block';
        } else if (selectedValue === 'per_kelas') {
            document.getElementById('filter-per-kelas').style.display = 'block';
        } else if (selectedValue === 'pemasukan') {
            document.getElementById('filter-tanggal').style.display = 'block';
        }
    }

    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#jenis_laporan').on('change', function() {
            toggleFilters();
        });
    } else {
        jenisLaporanSelect.addEventListener('change', toggleFilters);
    }

    toggleFilters();
});
</script>
<?= $this->endSection() ?>
