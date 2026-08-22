<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-wallet text-info mr-2"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('siswa') ?>">Data Siswa</a></li>
                    <li class="breadcrumb-item active">Detail Tabungan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Top Action Buttons & Quick Info -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
        <div>
            <a href="<?= base_url('siswa') ?>" class="btn btn-secondary mr-2 mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Siswa
            </a>
            <a href="<?= base_url('transaksi') ?>" class="btn btn-outline-primary mr-2 mb-2">
                <i class="fas fa-list-alt mr-1"></i> Transaksi Instan
            </a>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-info font-weight-bold shadow-sm mb-2">
                <i class="fas fa-print mr-1"></i> Cetak Buku Tabungan
            </button>
        </div>
    </div>

    <!-- Profile & Header Detail Card -->
    <div class="card card-info card-outline shadow-sm mb-4">
        <div class="card-header py-3 bg-light">
            <h3 class="card-title font-weight-bold text-dark mb-0">
                <i class="fas fa-id-card text-info mr-2"></i>Informasi Profil & Keuangan Siswa
            </h3>
            <div class="card-tools">
                <?php 
                    $status = strtolower($siswa['status_siswa'] ?? 'aktif');
                    $badgeClass = ($status === 'aktif') ? 'badge-success' : (($status === 'lulus') ? 'badge-info' : 'badge-secondary');
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-2 font-weight-bold" style="font-size: 13px;">
                    <i class="fas fa-info-circle mr-1"></i> Status: <?= strtoupper(esc($siswa['status_siswa'])) ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Avatar & Identity -->
                <div class="col-md-4 border-right mb-3 mb-md-0 text-center text-md-left">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center font-weight-bold mr-3 shadow-sm" style="width: 64px; height: 64px; font-size: 24px;">
                            <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-1"><?= esc($siswa['nama_lengkap']) ?></h4>
                            <p class="text-muted mb-0">NIS: <strong><?= esc($siswa['nis']) ?></strong></p>
                            <small class="text-muted">
                                <i class="fas fa-venus-mars mr-1"></i><?= ($siswa['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan' ?>
                                <?php if (!empty($siswa['tanggal_lahir'])) : ?>
                                    | <i class="fas fa-birthday-cake mr-1"></i><?= date('d-m-Y', strtotime($siswa['tanggal_lahir'])) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Academic & Class Info -->
                <div class="col-md-4 border-right mb-3 mb-md-0">
                    <div class="px-md-2">
                        <p class="mb-1">
                            <i class="fas fa-school text-primary mr-2" style="width: 18px;"></i>Kelas: 
                            <strong class="text-dark"><?= esc($riwayatKelas['nama_kelas'] ?? 'Belum Ditempatkan') ?></strong>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-calendar-alt text-info mr-2" style="width: 18px;"></i>Tahun Ajaran: 
                            <strong class="text-dark"><?= esc($riwayatKelas['nama_tahun_ajaran'] ?? 'Aktif') ?></strong>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-user-tie text-success mr-2" style="width: 18px;"></i>Wali Kelas: 
                            <strong class="text-dark"><?= esc($riwayatKelas['nama_wali_kelas'] ?? 'Belum Ditentukan') ?></strong>
                        </p>
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-map-marker-alt text-danger mr-2" style="width: 18px;"></i>Alamat: 
                            <?= esc($siswa['alamat'] ?: '-') ?>
                        </p>
                    </div>
                </div>

                <!-- Financial Summary Stats -->
                <div class="col-md-4">
                    <div class="px-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="text-muted"><i class="fas fa-arrow-down text-success mr-1"></i> Total Setoran (Masuk):</span>
                            <span class="font-weight-bold text-success" style="font-size: 15px;">Rp <?= number_format($stats['total_setor'], 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="text-muted"><i class="fas fa-arrow-up text-danger mr-1"></i> Total Penarikan (Keluar):</span>
                            <span class="font-weight-bold text-danger" style="font-size: 15px;">Rp <?= number_format($stats['total_tarik'], 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded border">
                            <span class="font-weight-bold text-dark"><i class="fas fa-wallet text-info mr-1"></i> Saldo Akhir Saat Ini:</span>
                            <span class="font-weight-bold text-primary" style="font-size: 18px;">Rp <?= number_format($stats['saldo_akhir'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Ledger Table Card -->
    <div class="card card-primary card-outline shadow-sm mb-5">
        <div class="card-header py-3 no-print">
            <form method="get" action="" class="form-inline w-100 flex-wrap justify-content-between">
                <div class="d-flex flex-wrap align-items-center mb-2 mb-md-0">
                    <h3 class="card-title font-weight-bold text-dark mr-3 mb-2 mb-md-0">
                        <i class="fas fa-history text-primary mr-2"></i>Riwayat Transaksi Tabungan (<?= $stats['total_transaksi'] ?> Transaksi)
                    </h3>
                </div>

                <div class="d-flex flex-wrap align-items-center">
                    <!-- Filter Tanggal Awal -->
                    <div class="input-group input-group-sm mr-2 mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="tgl_awal" class="form-control" value="<?= esc($tglAwal) ?>" title="Tanggal Awal">
                    </div>

                    <!-- Filter Tanggal Akhir -->
                    <div class="input-group input-group-sm mr-2 mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="tgl_akhir" class="form-control" value="<?= esc($tglAkhir) ?>" title="Tanggal Akhir">
                    </div>

                    <!-- Filter Jenis Transaksi -->
                    <select name="jenis_transaksi" class="form-control form-control-sm mr-2 mb-2">
                        <option value="">-- Semua Jenis --</option>
                        <option value="setor" <?= ($jenisFilter === 'setor') ? 'selected' : '' ?>>Setoran saja</option>
                        <option value="tarik" <?= ($jenisFilter === 'tarik') ? 'selected' : '' ?>>Penarikan saja</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-primary mr-1 mb-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <?php if ($tglAwal || $tglAkhir || $jenisFilter) : ?>
                        <a href="<?= base_url('siswa/' . $siswa['id'] . '/detail') ?>" class="btn btn-sm btn-secondary mb-2">
                            <i class="fas fa-sync-alt mr-1"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <!-- Kop Surat Khusus Mode Print -->
            <div class="print-header text-center py-3 border-bottom mb-3" style="display: none;">
                <h2 style="margin:0; font-weight:bold; font-size:20px;">BUKU TABUNGAN SISWA</h2>
                <h3 style="margin:5px 0 0 0; font-size:16px;">SD / MADRASAH TABUNGAN SISWA</h3>
                <p style="margin:2px 0 0 0; font-size:12px; color:#555;">Laporan Mutasi Rekening Tabungan Per-Siswa</p>
                <hr style="border:1px solid #000; margin:10px 0;">
                
                <table style="width:100%; text-align:left; font-size:12px; margin-bottom:15px;">
                    <tr>
                        <td width="15%"><strong>Nama Siswa</strong></td>
                        <td width="35%">: <?= esc($siswa['nama_lengkap']) ?></td>
                        <td width="15%"><strong>Kelas / TA</strong></td>
                        <td width="35%">: <?= esc($riwayatKelas['nama_kelas'] ?? '-') ?> (TA <?= esc($riwayatKelas['nama_tahun_ajaran'] ?? '-') ?>)</td>
                    </tr>
                    <tr>
                        <td><strong>NIS</strong></td>
                        <td>: <?= esc($siswa['nis']) ?></td>
                        <td><strong>Status Siswa</strong></td>
                        <td>: <?= strtoupper(esc($siswa['status_siswa'])) ?></td>
                    </tr>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="40">No</th>
                            <th width="140">Kode Transaksi</th>
                            <th width="140">Tanggal Transaksi</th>
                            <th width="150" class="no-print">Created At (Input)</th>
                            <th width="110">Jenis</th>
                            <th width="140" class="text-right">Jumlah (Rp)</th>
                            <th width="150" class="text-right">Saldo Setelah (Rp)</th>
                            <th>Keterangan</th>
                            <th width="130" class="no-print">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $runningSaldo = 0;
                        foreach ($transaksi as $idx => $t) : 
                            $isSetor = ($t['jenis_transaksi'] === 'setor');
                            $jumlah  = (float)$t['jumlah'];
                            $saldoSesudah = (float)$t['saldo_sesudah'];
                        ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $idx + 1 ?></td>
                                <td class="text-center"><span class="badge badge-light border font-weight-bold"><?= esc($t['kode_transaksi']) ?></span></td>
                                <td class="text-center font-weight-bold"><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                                <td class="text-center text-muted small no-print"><?= date('d-m-Y H:i:s', strtotime($t['created_at'])) ?></td>
                                <td class="text-center">
                                    <?php if ($isSetor) : ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down mr-1"></i> SETOR</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up mr-1"></i> TARIK</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right font-weight-bold <?= $isSetor ? 'text-success' : 'text-danger' ?>">
                                    <?= $isSetor ? '+' : '-' ?> Rp <?= number_format($jumlah, 0, ',', '.') ?>
                                </td>
                                <td class="text-right font-weight-bold text-primary">
                                    Rp <?= number_format($saldoSesudah, 0, ',', '.') ?>
                                </td>
                                <td><?= esc($t['keterangan'] ?: '-') ?></td>
                                <td class="no-print">
                                    <small><i class="fas fa-user-circle text-muted mr-1"></i><?= esc($t['nama_petugas'] ?? 'System') ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($transaksi)) : ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Belum ada riwayat transaksi tabungan untuk siswa ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="5" class="text-right">TOTAL MUTASI / SALDO:</td>
                            <td class="text-right text-success">+ Rp <?= number_format($stats['total_setor'], 0, ',', '.') ?></td>
                            <td class="text-right text-primary">Rp <?= number_format($stats['saldo_akhir'], 0, ',', '.') ?></td>
                            <td colspan="2">Total Penarikan: <span class="text-danger">- Rp <?= number_format($stats['total_tarik'], 0, ',', '.') ?></span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Footer Tanda Tangan -->
            <div class="print-footer mt-5 pt-3" style="display: none;">
                <table style="width: 100%; text-align: center; font-size: 12px;">
                    <tr>
                        <td width="50%">
                            Mengetahui,<br>
                            <strong>Wali Kelas / Petugas Tabungan</strong>
                            <br><br><br><br>
                            ( ________________________ )
                        </td>
                        <td width="50%">
                            Dicetak Pada: <?= date('d-m-Y H:i') ?><br>
                            <strong>Orang Tua / Wali Siswa</strong>
                            <br><br><br><br>
                            ( ________________________ )
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: #fff !important; }
    .main-sidebar, .main-header, .main-footer, .no-print, .content-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; background: #fff !important; }
    .card { border: none !important; box-shadow: none !important; }
    .card-header { display: none !important; }
    .print-header, .print-footer { display: block !important; }
    .table-responsive { overflow: visible !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { border: 1px solid #333 !important; padding: 6px 8px !important; }
}
</style>
<?= $this->endSection() ?>
