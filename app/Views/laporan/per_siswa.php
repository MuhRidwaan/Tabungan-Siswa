<h4>Laporan Mutasi Rekening</h4>
<strong>Nama Siswa:</strong> <?= esc($selectedSiswa['nama_lengkap']) ?><br>
<strong>NIS:</strong> <?= esc($selectedSiswa['nis']) ?><br>
<strong>Periode:</strong> <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?>

<table class="table table-bordered mt-3">
    <thead>
        <tr class="text-center">
            <th>Tanggal</th>
            <th>Kode</th>
            <th>Keterangan</th>
            <th>Setoran (Debit)</th>
            <th>Penarikan (Kredit)</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $saldo = $reportData[0]['saldo_sebelum'] ?? $selectedSiswa['saldo_akhir'];
        ?>
        <tr>
            <td colspan="5" class="text-right"><strong>Saldo Awal Periode</strong></td>
            <td class="text-right"><strong><?= number_format($saldo, 0, ',', '.') ?></strong></td>
        </tr>

        <?php foreach($reportData as $tx): ?>
        <tr>
            <td><?= date('d-m-Y H:i', strtotime($tx['created_at'])) ?></td>
            <td><?= esc($tx['kode_transaksi']) ?></td>
            <td><?= esc($tx['keterangan']) ?></td>
            <td class="text-right text-success">
                <?= ($tx['jenis_transaksi'] == 'setor') ? number_format($tx['jumlah'], 0, ',', '.') : '0' ?>
            </td>
            <td class="text-right text-danger">
                 <?= ($tx['jenis_transaksi'] == 'tarik') ? number_format($tx['jumlah'], 0, ',', '.') : '0' ?>
            </td>
            <td class="text-right">
                <?= number_format($tx['saldo_sesudah'], 0, ',', '.') ?>
            </td>
        </tr>
        <?php $saldo = $tx['saldo_sesudah']; ?>
        <?php endforeach; ?>

        <tr>
            <td colspan="5" class="text-right"><strong>Saldo Akhir Periode</strong></td>
            <td class="text-right"><strong><?= number_format($saldo, 0, ',', '.') ?></strong></td>
        </tr>
    </tbody>
</table>
