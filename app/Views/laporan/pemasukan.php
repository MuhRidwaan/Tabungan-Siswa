<h4>Laporan Pemasukan Biaya Administrasi</h4>
<strong>Periode:</strong> <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-chalkboard-teacher"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total untuk Guru</span>
                <span class="info-box-number">Rp <?= number_format($reportData['total_guru'] ?? 0, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
         <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-school"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total untuk Sekolah</span>
                <span class="info-box-number">Rp <?= number_format($reportData['total_sekolah'] ?? 0, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

<h5 class="mt-4">Rincian Pemasukan</h5>
<div class="table-responsive">
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th class="text-right">Untuk Guru</th>
                <th class="text-right">Untuk Sekolah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($reportDetails as $detail): ?>
            <tr>
                <td><?= date('d-m-Y H:i', strtotime($detail['tanggal_transaksi'])) ?></td>
                <td><?= esc($detail['kode_transaksi']) ?></td>
                <td class="text-right"><?= number_format($detail['jumlah_untuk_guru'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($detail['jumlah_untuk_sekolah'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
