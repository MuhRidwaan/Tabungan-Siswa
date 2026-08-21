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
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <form method="get" action="<?= base_url('laporan') ?>">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Jenis Laporan</label>
                            <select name="jenis_laporan" id="jenis_laporan" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="per_siswa" <?= ($jenisLaporan == 'per_siswa') ? 'selected' : '' ?>>Laporan per Siswa</option>
                                <option value="per_kelas" <?= ($jenisLaporan == 'per_kelas') ? 'selected' : '' ?>>Laporan per Kelas</option>
                                <option value="pemasukan" <?= ($jenisLaporan == 'pemasukan') ? 'selected' : '' ?>>Laporan Pemasukan</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Dynamic Filters -->
                    <div id="filter-per-siswa" class="col-lg-3 col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>Pilih Siswa</label>
                            <select name="siswa_id" class="form-control">
                                <option value="">-- Semua Siswa --</option>
                                <?php if(isset($listSiswa)): foreach ($listSiswa as $s) : ?>
                                    <option value="<?= $s['id'] ?>" <?= (isset($_GET['siswa_id']) && $_GET['siswa_id'] == $s['id']) ? 'selected' : '' ?>><?= esc($s['nama_lengkap']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <div id="filter-per-kelas" class="col-lg-3 col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">-- Semua Kelas --</option>
                                <?php if(isset($listKelas)): foreach ($listKelas as $k) : ?>
                                    <option value="<?= $k['id'] ?>" <?= (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <div id="filter-tanggal" class="col-lg-4 col-md-6" style="display: none;">
                       <div class="row">
                           <div class="col-6">
                               <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
                               </div>
                           </div>
                           <div class="col-6">
                               <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
                               </div>
                           </div>
                       </div>
                    </div>
                     <!-- End Dynamic Filters -->

                    <div class="col-lg-2 col-md-12 d-flex align-items-end">
                        <div class="form-group" style="width: 100%;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-search"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Display -->
    <?php if ($jenisLaporan && $reportData !== null) : ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hasil Laporan</h3>
            </div>
            <div class="card-body">
                <?php if ($jenisLaporan == 'per_siswa' && !empty($reportData)) : ?>
                    <?= $this->include('laporan/per_siswa') ?>
                <?php elseif ($jenisLaporan == 'per_kelas') : ?>
                    <?= $this->include('laporan/per_kelas') ?>
                <?php elseif ($jenisLaporan == 'pemasukan') : ?>
                    <?= $this->include('laporan/pemasukan') ?>
                <?php else: ?>
                    <div class="alert alert-warning">Silakan pilih filter yang sesuai atau tidak ada data untuk ditampilkan.</div>
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

    jenisLaporanSelect.addEventListener('change', toggleFilters);
    toggleFilters(); // Run on page load
});
</script>
<?= $this->endSection() ?>
