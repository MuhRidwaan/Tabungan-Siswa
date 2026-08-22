<div class="mb-3">
    <h4 class="font-weight-bold text-dark"><i class="fas fa-coins text-warning mr-2"></i>Rekapitulasi Bagi Hasil Kas Komisi Admin</h4>
    <p class="text-muted small">Periode: <strong><?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></strong></p>
</div>

<div class="row mb-4">
    <?php
    $sumSetoran = 0;
    foreach ($reportData as $r) {
        $sumSetoran += (float)$r['total_setor'];
    }
    $sumGuru = $sumSetoran * ($persenGuru / 100);
    $sumSekolah = $sumSetoran * ($persenSekolah / 100);
    $sumTotalKomisi = $sumGuru + $sumSekolah;
    ?>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body p-3 text-center">
                <span class="small uppercase font-weight-bold">Total Volume Setoran</span>
                <h4 class="font-weight-bold mb-0 mt-1">Rp <?= number_format($sumSetoran, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body p-3 text-center">
                <span class="small uppercase font-weight-bold">Porsi Kas Guru (<?= esc($persenGuru) ?>%)</span>
                <h4 class="font-weight-bold mb-0 mt-1">Rp <?= number_format($sumGuru, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body p-3 text-center">
                <span class="small uppercase font-weight-bold">Porsi Kas Sekolah (<?= esc($persenSekolah) ?>%)</span>
                <h4 class="font-weight-bold mb-0 mt-1">Rp <?= number_format($sumSekolah, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body p-3 text-center">
                <span class="small uppercase font-weight-bold">Total Komisi Terkumpul</span>
                <h4 class="font-weight-bold mb-0 mt-1">Rp <?= number_format($sumTotalKomisi, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped table-hover mt-3 mb-0 data-table">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="50">No</th>
            <th width="130">Tanggal</th>
            <th>Kelas</th>
            <th width="120">Jumlah Trx</th>
            <th width="180" class="text-right">Volume Setor (Rp)</th>
            <th width="160" class="text-right">Kas Guru (<?= esc($persenGuru) ?>%)</th>
            <th width="160" class="text-right">Kas Sekolah (<?= esc($persenSekolah) ?>%)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reportData as $idx => $r) : 
            $setor = (float)$r['total_setor'];
            $alokGuru = $setor * ($persenGuru / 100);
            $alokSekolah = $setor * ($persenSekolah / 100);
        ?>
            <tr>
                <td class="text-center font-weight-bold"><?= $idx + 1 ?></td>
                <td class="text-center font-weight-bold"><?= date('d-m-Y', strtotime($r['tgl'])) ?></td>
                <td><span class="badge badge-info"><i class="fas fa-school mr-1"></i><?= esc($r['nama_kelas'] ?: 'Umum/Kolektif') ?></span></td>
                <td class="text-center font-weight-bold"><?= number_format($r['total_trx']) ?> Transaksi</td>
                <td class="text-right font-weight-bold text-success">Rp <?= number_format($setor, 0, ',', '.') ?></td>
                <td class="text-right font-weight-bold text-primary">Rp <?= number_format($alokGuru, 0, ',', '.') ?></td>
                <td class="text-right font-weight-bold text-info">Rp <?= number_format($alokSekolah, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reportData)) : ?>
            <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Tidak ada data transaksi setoran pada periode ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>