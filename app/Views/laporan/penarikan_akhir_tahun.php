<div class="mb-3">
    <h4 class="font-weight-bold text-dark"><i class="fas fa-graduation-cap text-purple mr-2"></i>Rekap Penutupan Buku & Penarikan Akhir Tahun</h4>
    <p class="text-muted small">Periode: <strong><?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></strong></p>
</div>

<table class="table table-bordered table-striped table-hover mt-3 mb-0 data-table">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="50">No</th>
            <th width="140">Tanggal Penarikan</th>
            <th width="120">Kode Trx</th>
            <th width="100">NIS</th>
            <th>Nama Siswa</th>
            <th width="130">Kelas</th>
            <th width="160" class="text-right">Nominal Tarik (Rp)</th>
            <th>Keterangan</th>
            <th width="120">Petugas</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $totalPenarikanAkhir = 0;
        foreach ($reportData as $idx => $t) : 
            $totalPenarikanAkhir += (float)$t['jumlah'];
        ?>
            <tr>
                <td class="text-center font-weight-bold"><?= $idx + 1 ?></td>
                <td class="text-center small font-weight-bold"><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'] ?? $t['created_at'])) ?></td>
                <td class="text-center"><span class="badge badge-secondary"><?= esc($t['kode_transaksi']) ?></span></td>
                <td><span class="badge badge-light border"><?= esc($t['nis']) ?></span></td>
                <td class="font-weight-bold text-dark"><?= esc($t['nama_lengkap']) ?></td>
                <td><span class="badge badge-info"><i class="fas fa-school mr-1"></i><?= esc($t['nama_kelas'] ?: '-') ?></span></td>
                <td class="text-right font-weight-bold text-danger">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                <td><?= esc($t['keterangan']) ?></td>
                <td class="small"><i class="fas fa-user-circle text-muted mr-1"></i><?= esc($t['nama_petugas'] ?: 'Admin') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reportData)) : ?>
            <tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada data penarikan akhir tahun pada periode ini.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="bg-light font-weight-bold">
            <td colspan="6" class="text-right">TOTAL DANA PENARIKAN AKHIR TAHUN</td>
            <td class="text-right text-danger" style="font-size: 14px;">Rp <?= number_format($totalPenarikanAkhir, 0, ',', '.') ?></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>