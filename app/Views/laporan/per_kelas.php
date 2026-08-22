<h4 class="font-weight-bold">Rekapitulasi Saldo Tabungan Siswa per Kelas</h4>
<div class="mb-3">
    <strong>Kelas:</strong> <?= esc($selectedKelas['nama_kelas'] ?? '-') ?><br>
    <strong>Tahun Ajaran:</strong> <?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Aktif') ?><br>
</div>

<table class="table table-bordered table-striped mt-3 mb-0">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="60">No</th>
            <th width="140">NIS</th>
            <th>Nama Siswa</th>
            <th width="180" class="text-right">Saldo Tabungan (Rp)</th>
            <?php if (!empty($includeAlokasi)) : ?>
                <th width="160" class="text-right">Alokasi Guru (<?= esc($persenGuru) ?>%)</th>
                <th width="160" class="text-right">Alokasi Sekolah (<?= esc($persenSekolah) ?>%)</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $totalSaldo = 0;
        $totalGuru = 0;
        $totalSekolah = 0;
        foreach($reportData as $key => $siswa): 
            $saldo = $siswa['saldo_akhir'];
            $totalSaldo += $saldo;
            $alokGuru = $saldo * (($persenGuru ?? 1.0) / 100);
            $alokSekolah = $saldo * (($persenSekolah ?? 1.5) / 100);
            $totalGuru += $alokGuru;
            $totalSekolah += $alokSekolah;
        ?>
        <tr>
            <td class="text-center"><?= $key + 1 ?></td>
            <td class="text-center"><span class="badge badge-light border"><?= esc($siswa['nis']) ?></span></td>
            <td class="font-weight-bold"><?= esc($siswa['nama_lengkap']) ?></td>
            <td class="text-right font-weight-bold text-success">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
            <?php if (!empty($includeAlokasi)) : ?>
                <td class="text-right text-primary small">Rp <?= number_format($alokGuru, 0, ',', '.') ?></td>
                <td class="text-right text-info small">Rp <?= number_format($alokSekolah, 0, ',', '.') ?></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="bg-light font-weight-bold">
            <th colspan="3" class="text-right">TOTAL AKUMULASI TABUNGAN KELAS</th>
            <th class="text-right text-success">Rp <?= number_format($totalSaldo, 0, ',', '.') ?></th>
            <?php if (!empty($includeAlokasi)) : ?>
                <th class="text-right text-primary">Rp <?= number_format($totalGuru, 0, ',', '.') ?></th>
                <th class="text-right text-info">Rp <?= number_format($totalSekolah, 0, ',', '.') ?></th>
            <?php endif; ?>
        </tr>
    </tfoot>
</table>

<?php if (!empty($includeAlokasi)) : ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-primary"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Total Alokasi Kas Guru (<?= esc($persenGuru) ?>%)</span>
                    <span class="info-box-number text-primary font-weight-bold">Rp <?= number_format($totalGuru, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-info"><i class="fas fa-school"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Total Alokasi Kas Sekolah (<?= esc($persenSekolah) ?>%)</span>
                    <span class="info-box-number text-info font-weight-bold">Rp <?= number_format($totalSekolah, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Total Tabungan Netto</span>
                    <span class="info-box-number text-success font-weight-bold">Rp <?= number_format($totalSaldo, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
