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

<style>
@media print {
    /* Sembunyikan elemen UI bawaan AdminLTE */
    .main-header, .main-sidebar, .main-footer, .content-header, .card-header, .no-print, nav, .btn, .breadcrumb, form {
        display: none !important;
    }
    body, .wrapper, .content-wrapper, .container-fluid, .card, .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
        color: #000 !important;
    }
    .print-area {
        display: block !important;
        width: 100% !important;
    }
    .table-bordered th, .table-bordered td {
        border: 1px solid #000 !important;
    }
}
</style>

<div class="container-fluid">
    <!-- Form Filter -->
    <div class="card card-outline card-info no-print">
        <div class="card-header py-3">
            <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-info mr-2"></i>Filter Generator Laporan</h3>
        </div>
        <form method="get" action="<?= base_url('laporan') ?>" id="formFilterLaporan">
            <div class="card-body bg-light">
                <div class="row align-items-end">
                    <!-- Jenis Laporan -->
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small font-weight-bold">Jenis Laporan</label>
                        <select name="jenis_laporan" id="jenis_laporan" class="form-control select2" required>
                            <option value="">-- Pilih Jenis Laporan --</option>
                            <option value="per_siswa" <?= ($jenisLaporan == 'per_siswa') ? 'selected' : '' ?>>📊 Laporan per Siswa</option>
                            <option value="per_kelas" <?= ($jenisLaporan == 'per_kelas') ? 'selected' : '' ?>>🏫 Rekapitulasi per Kelas</option>
                            <option value="pemasukan" <?= ($jenisLaporan == 'pemasukan') ? 'selected' : '' ?>>💰 Laporan Pemasukan Kas</option>
                        </select>
                    </div>
                    
                    <!-- Tahun Ajaran -->
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
                    <div id="filter-per-kelas" class="col-lg-3 col-md-6 mb-2" style="display: none;">
                        <label class="small font-weight-bold">Filter Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control select2">
                            <option value="">-- Pilih Kelas --</option>
                            <?php if(isset($listKelas)): foreach ($listKelas as $k) : ?>
                                <option value="<?= $k['id'] ?>" <?= (isset($selectedKelasId) && $selectedKelasId == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div id="filter-per-siswa" class="col-lg-3 col-md-6 mb-2" style="display: none;">
                        <label class="small font-weight-bold">Pilih Siswa</label>
                        <select name="siswa_id" id="siswa_id" class="form-control select2">
                            <option value="">-- Pilih Siswa --</option>
                            <?php if(isset($listSiswa)): foreach ($listSiswa as $s) : ?>
                                <option value="<?= $s['id'] ?>" <?= (isset($_GET['siswa_id']) && $_GET['siswa_id'] == $s['id']) ? 'selected' : '' ?>><?= esc($s['nis']) ?> - <?= esc($s['nama_lengkap']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div id="filter-tanggal" class="col-lg-3 col-md-6 mb-2" style="display: none;">
                       <div class="row">
                           <div class="col-6">
                                <label class="small font-weight-bold">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
                           </div>
                           <div class="col-6">
                                <label class="small font-weight-bold">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
                           </div>
                       </div>
                    </div>
                    <!-- End Dynamic Filters -->

                    <div class="col-lg-2 col-md-12 mb-2">
                        <button type="submit" class="btn btn-info btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-play mr-1"></i> Generate
                        </button>
                    </div>

                    <!-- Checkbox Include Alokasi Bagi Hasil Kas -->
                    <div class="col-12 mt-2">
                        <div class="custom-control custom-checkbox bg-white p-2 border rounded">
                            <input type="checkbox" class="custom-control-input" id="include_alokasi" name="include_alokasi" value="1" <?= (!empty($includeAlokasi) ? 'checked' : '') ?>>
                            <label class="custom-control-label font-weight-bold text-dark" for="include_alokasi">
                                <i class="fas fa-coins text-warning mr-1"></i> Tampilkan Rincian Bagi Hasil Kas (Sekolah <?= esc($persenSekolah ?? '1.5') ?>% & Guru <?= esc($persenGuru ?? '1.0') ?>%)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Display area -->
    <?php if ($jenisLaporan && $reportData !== null) : ?>
        <div class="card card-primary card-outline print-area">
            <!-- Header Kartu UI (Di-hide saat diprint) -->
            <div class="card-header border-0 py-3 no-print">
                <div class="row w-100 align-items-center m-0">
                    <div class="col-md-6 p-0 mb-2 mb-md-0">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-file-alt text-primary mr-2"></i> Hasil Laporan Tabungan</h3>
                    </div>
                    <div class="col-md-6 p-0 text-md-right text-left">
                        <a href="<?= base_url('laporan/export?' . ($_SERVER['QUERY_STRING'] ?? '')) ?>" class="btn btn-success mr-1 shadow-sm">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel (.xls)
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-primary shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Laporan (Print/PDF)
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Header Kop Surat Resmi Sekolah (Hanya Muncul Saat Diprint) -->
                <div class="print-header text-center mb-4 pb-2 border-bottom border-dark d-none d-print-block">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <img src="<?= base_url('dist/img/logo_sekolah_dasar.png') ?>" alt="Logo Sekolah" style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px;">
                        <div>
                            <h3 class="font-weight-bold text-uppercase mb-0" style="font-size: 18pt; letter-spacing: 1px;">
                                <?= esc($pengaturan['nama_sekolah'] ?? 'SEKOLAH / MADRASAH TABUNGAN SISWA') ?>
                            </h3>
                            <p class="mb-0 text-muted small">
                                <?= esc($pengaturan['alamat_sekolah'] ?? 'Sistem Informasi Manajemen Tabungan Siswa') ?>
                            </p>
                        </div>
                    </div>
                    <div style="border-top: 3px double #000; margin-top: 8px;"></div>
                </div>

                <!-- Konten Laporan -->
                <?php if ($jenisLaporan == 'per_siswa' && !empty($reportData)) : ?>
                    <?= $this->include('laporan/per_siswa') ?>
                <?php elseif ($jenisLaporan == 'per_kelas') : ?>
                    <?= $this->include('laporan/per_kelas') ?>
                <?php elseif ($jenisLaporan == 'pemasukan') : ?>
                    <?= $this->include('laporan/pemasukan') ?>
                <?php else: ?>
                    <div class="alert alert-warning py-3"><i class="fas fa-exclamation-triangle mr-1"></i> Silakan pilih filter yang sesuai atau tidak ada data untuk ditampilkan.</div>
                <?php endif; ?>

                <!-- Tanda Tangan Cetak (Hanya Muncul Saat Diprint) -->
                <div class="row mt-5 pt-4 d-none d-print-flex">
                    <div class="col-6 text-center">
                        <p class="mb-1">Mengetahui,</p>
                        <p class="font-weight-bold mb-5">Kepala Sekolah</p>
                        <br><br>
                        <p class="font-weight-bold mb-0"><u>( ________________________ )</u></p>
                        <p class="small text-muted">NIP. ........................................</p>
                    </div>
                    <div class="col-6 text-center">
                        <p class="mb-1">Tanggal Cetak: <?= date('d/m/Y') ?></p>
                        <p class="font-weight-bold mb-5">Bendahara / Pengelola Tabungan</p>
                        <br><br>
                        <p class="font-weight-bold mb-0"><u>( <?= esc(auth()->user()->username ?? 'Petugas Tabungan') ?> )</u></p>
                        <p class="small text-muted">NIP. ........................................</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function toggleFilters() {
        const jenis = $('#jenis_laporan').val();
        $('#filter-per-kelas').hide();
        $('#filter-per-siswa').hide();
        $('#filter-tanggal').hide();
        
        if (jenis === 'per_siswa') {
            $('#filter-per-kelas').show();
            $('#filter-per-siswa').show();
            $('#filter-tanggal').show();
        } else if (jenis === 'per_kelas') {
            $('#filter-per-kelas').show();
        } else if (jenis === 'pemasukan') {
            $('#filter-tanggal').show();
        }
    }

    function updateSiswaByKelas() {
        const jenis = $('#jenis_laporan').val();
        if (jenis !== 'per_siswa') return;

        const kelasId = $('#kelas_id').val();
        const tahunId = $('#tahun_ajaran_id').val();
        const siswaSelect = $('#siswa_id');

        if (!kelasId) return;

        siswaSelect.html('<option value="">Memuat daftar siswa...</option>');

        fetch(`<?= base_url('transaksi/get-siswa-by-kelas') ?>/${kelasId}?tahun_ajaran_id=${tahunId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            let options = '<option value="">-- Pilih Siswa Kelas --</option>';
            const currentSelected = '<?= $_GET['siswa_id'] ?? '' ?>';
            data.forEach(s => {
                const sId = s.siswa_id || s.id;
                const isSelected = (sId == currentSelected) ? 'selected' : '';
                options += `<option value="${sId}" ${isSelected}>${s.nis} - ${s.nama_lengkap}</option>`;
            });
            siswaSelect.html(options);
            if ($.fn.select2) {
                siswaSelect.trigger('change.select2');
            }
        })
        .catch(err => {
            console.error('Error fetching siswa:', err);
        });
    }

    if (typeof $ !== 'undefined') {
        $('#jenis_laporan').on('change select2:select', function() {
            toggleFilters();
        });

        $('#kelas_id, #tahun_ajaran_id').on('change select2:select', function() {
            updateSiswaByKelas();
        });
    }

    toggleFilters();
});
</script>
<?= $this->endSection() ?>
