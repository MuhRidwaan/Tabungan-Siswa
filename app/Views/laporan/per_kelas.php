<h4>Laporan Saldo Siswa per Kelas</h4>
<strong>Kelas:</strong> <?= esc($selectedKelas['nama_kelas']) ?><br>
<strong>Tahun Ajaran:</strong> Aktif<br>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th class="text-right">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $totalSaldo = 0;
        foreach($reportData as $key => $siswa): 
        $totalSaldo += $siswa['saldo_akhir'];
        ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= esc($siswa['nis']) ?></td>
            <td><?= esc($siswa['nama_lengkap']) ?></td>
            <td class="text-right"><?= number_format($siswa['saldo_akhir'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-right">Total Saldo Kelas</th>
            <th class="text-right"><?= number_format($totalSaldo, 0, ',', '.') ?></th>
        </tr>
    </tfoot>
</table>
