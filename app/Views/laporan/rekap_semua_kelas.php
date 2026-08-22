<div class="mb-3">
    <h4 class="font-weight-bold text-dark"><i class="fas fa-school text-primary mr-2"></i>Rekapitulasi Total Tabungan Seluruh Kelas</h4>
    <p class="text-muted small">Tahun Ajaran: <strong><?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Semua') ?></strong> | Tanggal Cetak: <strong><?= date('d M Y') ?></strong></p>
</div>

<table class="table table-bordered table-striped table-hover mt-3 mb-0 data-table">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="50">No</th>
            <th>Nama Kelas</th>
            <th width="120">Tingkat</th>
            <th>Wali Kelas</th>
            <th width="140">Jumlah Siswa</th>
            <th width="200" class="text-right">Total Saldo Kas Kelas (Rp)</th>
            <?php if (!empty($includeAlokasi)) : ?>
                <th width="160" class="text-right">Kas Guru (<?= esc($persenGuru) ?>%)</th>
                <th width="160" class="text-right">Kas Sekolah (<?= esc($persenSekolah) ?>%)</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $totalSiswaSemua = 0;
        $totalSaldoSemua = 0;
        $totalGuruSemua = 0;
        $totalSekolahSemua = 0;

        foreach ($reportData as $idx => $k) : 
            $totalSiswaSemua += $k['total_siswa'];
            $totalSaldoSemua += $k['total_saldo'];
            $alokGuru = $k['total_saldo'] * ($persenGuru / 100);
            $alokSekolah = $k['total_saldo'] * ($persenSekolah / 100);
            $totalGuruSemua += $alokGuru;
            $totalSekolahSemua += $alokSekolah;
        ?>
            <tr>
                <td class="text-center font-weight-bold"><?= $idx + 1 ?></td>
                <td class="font-weight-bold">
                    <span class="badge badge-info p-2 font-weight-bold" style="font-size: 13px;">
                        <i class="fas fa-school mr-1"></i><?= esc($k['nama_kelas']) ?>
                    </span>
                </td>
                <td class="text-center"><span class="badge badge-secondary"><?= esc($k['tingkat']) ?></span></td>
                <td><i class="fas fa-user-tie text-muted mr-1"></i><?= esc($k['nama_wali'] ?: 'Belum Ditentukan') ?></td>
                <td class="text-center font-weight-bold"><span class="badge badge-light border px-2 py-1"><?= number_format($k['total_siswa']) ?> Siswa</span></td>
                <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                    Rp <?= number_format($k['total_saldo'], 0, ',', '.') ?>
                </td>
                <?php if (!empty($includeAlokasi)) : ?>
                    <td class="text-right text-primary small font-weight-bold">
                        Rp <?= number_format($alokGuru, 0, ',', '.') ?>
                    </td>
                    <td class="text-right text-info small font-weight-bold">
                        Rp <?= number_format($alokSekolah, 0, ',', '.') ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reportData)) : ?>
            <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Tidak ada data kelas untuk ditampilkan.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="bg-light font-weight-bold" style="font-size: 15px;">
            <td colspan="4" class="text-right">TOTAL KESELURUHAN SEKOLAH</td>
            <td class="text-center text-primary"><?= number_format($totalSiswaSemua) ?> Siswa</td>
            <td class="text-right text-success">Rp <?= number_format($totalSaldoSemua, 0, ',', '.') ?></td>
            <?php if (!empty($includeAlokasi)) : ?>
                <td class="text-right text-primary">Rp <?= number_format($totalGuruSemua, 0, ',', '.') ?></td>
                <td class="text-right text-info">Rp <?= number_format($totalSekolahSemua, 0, ',', '.') ?></td>
            <?php endif; ?>
        </tr>
    </tfoot>
</table>