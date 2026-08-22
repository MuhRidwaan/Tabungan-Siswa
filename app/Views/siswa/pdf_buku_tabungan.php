<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Tabungan - <?= esc($siswa['nama_lengkap']) ?></title>
    <style>
        @page { margin: 12mm 10mm 12mm 10mm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; }
        .kop-surat { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 6px; margin-bottom: 12px; }
        .kop-title h2 { margin: 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .kop-title h3 { margin: 2px 0; font-size: 12px; color: #0d9488; }
        .kop-title p { margin: 0; font-size: 9px; color: #64748b; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10px; }
        .info-table td { padding: 3px 5px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: fixed; word-wrap: break-word; }
        .data-table th { background-color: #f1f5f9; color: #0f172a; font-weight: bold; border: 1px solid #cbd5e1; padding: 5px 4px; text-align: center; font-size: 9px; }
        .data-table td { border: 1px solid #cbd5e1; padding: 4px 4px; font-size: 9px; word-break: break-word; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #16a34a; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .text-primary { color: #0284c7; font-weight: bold; }
        .footer-ttd { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .footer-ttd td { text-align: center; vertical-align: top; font-size: 10px; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <div class="kop-title">
            <h2>BUKU TABUNGAN SISWA</h2>
            <h3><?= esc(!empty($pengaturan['nama_sekolah']) ? $pengaturan['nama_sekolah'] : 'SDN PADAMAMUR') ?></h3>
            <p><?= esc(!empty($pengaturan['alamat_sekolah']) ? $pengaturan['alamat_sekolah'] : 'SDN Padamamur - Sistem Informasi Manajemen Tabungan Siswa') ?></p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Siswa</strong></td>
            <td width="35%">: <?= esc($siswa['nama_lengkap']) ?></td>
            <td width="15%"><strong>Kelas / TA</strong></td>
            <td width="35%">: <?= esc($riwayatKelas['nama_kelas'] ?? '-') ?> (TA: <?= esc($riwayatKelas['nama_tahun_ajaran'] ?? '-') ?>)</td>
        </tr>
        <tr>
            <td><strong>NIS</strong></td>
            <td>: <?= esc($siswa['nis']) ?></td>
            <td><strong>Status Siswa</strong></td>
            <td>: <?= strtoupper(esc($siswa['status_siswa'])) ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="21%">Kode Transaksi</th>
                <th width="16%">Tanggal</th>
                <th width="10%">Jenis</th>
                <th width="15%" class="text-right">Jumlah (Rp)</th>
                <th width="15%" class="text-right">Saldo (Rp)</th>
                <th width="18%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalSetor = 0;
            $totalTarik = 0;
            foreach ($transaksiList as $idx => $t) : 
                $isSetor = ($t['jenis_transaksi'] === 'setor');
                if ($isSetor) {
                    $totalSetor += (float)$t['jumlah'];
                } else {
                    $totalTarik += (float)$t['jumlah'];
                }
            ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td class="text-center"><?= esc($t['kode_transaksi']) ?></td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                    <td class="text-center <?= $isSetor ? 'text-success' : 'text-danger' ?>"><?= strtoupper($t['jenis_transaksi']) ?></td>
                    <td class="text-right <?= $isSetor ? 'text-success' : 'text-danger' ?>"><?= $isSetor ? '+' : '-' ?> Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                    <td class="text-right text-primary">Rp <?= number_format($t['saldo_sesudah'], 0, ',', '.') ?></td>
                    <td><?= esc($t['keterangan'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($transaksiList)) : ?>
                <tr><td colspan="7" class="text-center">Belum ada transaksi.</td></tr>
            <?php else : ?>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAL MUTASI / SALDO AKHIR:</td>
                    <td class="text-right text-success">+ Rp <?= number_format($totalSetor, 0, ',', '.') ?></td>
                    <td class="text-right text-primary">Rp <?= number_format($siswa['saldo_akhir'], 0, ',', '.') ?></td>
                    <td>Total Penarikan: <span class="text-danger">- Rp <?= number_format($totalTarik, 0, ',', '.') ?></span></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-ttd">
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
</body>
</html>