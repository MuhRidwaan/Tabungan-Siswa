<div class="mb-3">
    <h4 class="font-weight-bold text-dark"><i class="fas fa-trophy text-warning mr-2"></i>Ranking Siswa Terajin Menabung (Top Savers)</h4>
    <p class="text-muted small">Daftar Top 20 Siswa dengan Saldo Tabungan Terbesar</p>
</div>

<table class="table table-bordered table-striped table-hover mt-3 mb-0 data-table">
    <thead class="bg-light">
        <tr class="text-center">
            <th width="70">Peringkat</th>
            <th width="110">NIS</th>
            <th>Nama Lengkap Siswa</th>
            <th width="140">Kelas Saat Ini</th>
            <th width="60">L/P</th>
            <th width="200" class="text-right">Saldo Tabungan (Rp)</th>
            <th width="90">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reportData as $idx => $s) : ?>
            <tr class="<?= ($idx < 3) ? 'font-weight-bold' : '' ?>">
                <td class="text-center">
                    <?php if ($idx == 0) : ?>
                        <span class="badge badge-warning text-dark p-2" style="font-size:14px;"><i class="fas fa-crown mr-1"></i> Juara 1</span>
                    <?php elseif ($idx == 1) : ?>
                        <span class="badge badge-secondary p-2" style="font-size:13px;"><i class="fas fa-medal mr-1"></i> Juara 2</span>
                    <?php elseif ($idx == 2) : ?>
                        <span class="badge badge-danger p-2" style="font-size:13px;"><i class="fas fa-award mr-1"></i> Juara 3</span>
                    <?php else : ?>
                        <span class="badge badge-light border">#<?= $idx + 1 ?></span>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-secondary"><?= esc($s['nis']) ?></span></td>
                <td class="font-weight-bold text-dark"><?= esc($s['nama_lengkap']) ?></td>
                <td>
                    <?php if (!empty($s['nama_kelas'])) : ?>
                        <span class="badge badge-info"><i class="fas fa-school mr-1"></i><?= esc($s['nama_kelas']) ?></span>
                    <?php else : ?>
                        <span class="badge badge-light text-muted">Belum Ditempatkan</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><span class="badge badge-light border"><?= esc($s['jenis_kelamin']) ?></span></td>
                <td class="text-right font-weight-bold text-success" style="font-size:14px;">
                    Rp <?= number_format($s['saldo_akhir'], 0, ',', '.') ?>
                </td>
                <td class="text-center">
                    <span class="badge badge-success"><?= ucfirst(esc($s['status_siswa'])) ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reportData)) : ?>
            <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Tidak ada data siswa untuk ditampilkan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>