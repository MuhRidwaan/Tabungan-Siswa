<h4 class="font-weight-bold">Laporan Mutasi Rekening Tabungan</h4>
<div class="mb-3">
    <strong>Nama Siswa:</strong> <?= esc($selectedSiswa['nama_lengkap']) ?><br>
    <strong>NIS:</strong> <?= esc($selectedSiswa['nis']) ?><br>
    <strong>Periode:</strong> <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?>
</div>

<table class="table table-bordered table-striped mt-3 mb-0">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="140">Tanggal</th>
            <th width="130">Kode</th>
            <th>Keterangan</th>
            <th width="130" class="text-right">Setoran (Debit)</th>
            <?php if (!empty($includeAlokasi)) : ?>
                <th width="120" class="text-right">Guru (<?= esc($persenGuru) ?>%)</th>
                <th width="120" class="text-right">Sekolah (<?= esc($persenSekolah) ?>%)</th>
            <?php endif; ?>
            <th width="130" class="text-right">Penarikan (Kredit)</th>
            <th width="140" class="text-right">Saldo</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $saldo = $reportData[0]['saldo_sebelum'] ?? $selectedSiswa['saldo_akhir'];
        $totalSetor = 0;
        $totalTarik = 0;
        $totalGuru = 0;
        $totalSekolah = 0;
        $colspanLeft = !empty($includeAlokasi) ? 5 : 3;
        ?>
        <tr>
            <td colspan="<?= $colspanLeft + 2 ?>" class="text-right font-weight-bold">Saldo Awal Periode</td>
            <td class="text-right font-weight-bold">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
        </tr>

        <?php foreach($reportData as $tx): 
            $jml = $tx['jumlah'];
            if ($tx['jenis_transaksi'] == 'setor') {
                $totalSetor += $jml;
                $alokGuru = $jml * (($persenGuru ?? 1.0) / 100);
                $alokSekolah = $jml * (($persenSekolah ?? 1.5) / 100);
                $totalGuru += $alokGuru;
                $totalSekolah += $alokSekolah;
            } else {
                $totalTarik += $jml;
                $alokGuru = 0;
                $alokSekolah = 0;
            }
        ?>
        <tr>
            <td class="text-center"><?= date('d-m-Y H:i', strtotime($tx['tanggal_transaksi'] ?? $tx['created_at'])) ?></td>
            <td class="text-center"><span class="badge badge-secondary"><?= esc($tx['kode_transaksi']) ?></span></td>
            <td><?= esc($tx['keterangan']) ?></td>
            <td class="text-right text-success font-weight-bold">
                <?= ($tx['jenis_transaksi'] == 'setor') ? '+ Rp ' . number_format($jml, 0, ',', '.') : '-' ?>
            </td>
            <?php if (!empty($includeAlokasi)) : ?>
                <td class="text-right text-primary small">
                    <?= ($tx['jenis_transaksi'] == 'setor') ? 'Rp ' . number_format($alokGuru, 0, ',', '.') : '-' ?>
                </td>
                <td class="text-right text-info small">
                    <?= ($tx['jenis_transaksi'] == 'setor') ? 'Rp ' . number_format($alokSekolah, 0, ',', '.') : '-' ?>
                </td>
            <?php endif; ?>
            <td class="text-right text-danger font-weight-bold">
                 <?= ($tx['jenis_transaksi'] == 'tarik') ? '- Rp ' . number_format($jml, 0, ',', '.') : '-' ?>
            </td>
            <td class="text-right font-weight-bold">
                Rp <?= number_format($tx['saldo_sesudah'], 0, ',', '.') ?>
            </td>
        </tr>
        <?php $saldo = $tx['saldo_sesudah']; ?>
        <?php endforeach; ?>

        <tr class="bg-light font-weight-bold">
            <td colspan="3" class="text-right">TOTAL MUTASI PERIODE INI</td>
            <td class="text-right text-success">+ Rp <?= number_format($totalSetor, 0, ',', '.') ?></td>
            <?php if (!empty($includeAlokasi)) : ?>
                <td class="text-right text-primary">Rp <?= number_format($totalGuru, 0, ',', '.') ?></td>
                <td class="text-right text-info">Rp <?= number_format($totalSekolah, 0, ',', '.') ?></td>
            <?php endif; ?>
            <td class="text-right text-danger">- Rp <?= number_format($totalTarik, 0, ',', '.') ?></td>
            <td class="text-right text-dark">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
        </tr>
    </tbody>
</table>

<?php if (!empty($includeAlokasi)) : ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-primary"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Alokasi Kas Guru (<?= esc($persenGuru) ?>%)</span>
                    <span class="info-box-number text-primary font-weight-bold">Rp <?= number_format($totalGuru, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-info"><i class="fas fa-school"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Alokasi Kas Sekolah (<?= esc($persenSekolah) ?>%)</span>
                    <span class="info-box-number text-info font-weight-bold">Rp <?= number_format($totalSekolah, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light border">
                <span class="info-box-icon bg-success"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-dark font-weight-bold">Saldo Netto Tabungan</span>
                    <span class="info-box-number text-success font-weight-bold">Rp <?= number_format($saldo, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
