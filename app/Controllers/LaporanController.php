<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Models\AlokasiBiayaAdmin;
use App\Models\RiwayatKelasSiswa;

class LaporanController extends BaseController
{
    public function index()
    {
        $jenisLaporan    = $this->request->getGet('jenis_laporan');
        $startDate       = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate         = $this->request->getGet('end_date') ?: date('Y-m-t');
        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');
        $includeAlokasi  = (int) $this->request->getGet('include_alokasi');

        // Inisialisasi Models
        $siswaModel       = new Siswa();
        $kelasModel       = new Kelas();
        $transaksiModel   = new Transaksi();
        $alokasiModel     = new AlokasiBiayaAdmin();
        $tahunAjaranModel = new TahunAjaran();
        $pengaturanModel  = new \App\Models\Pengaturan();

        $tahunAjaranList = $tahunAjaranModel->orderBy('id', 'DESC')->findAll();
        $tahunAktif      = $tahunAjaranModel->where('status', 'aktif')->first();
        $pengaturan      = $pengaturanModel->getPengaturanAsArray();

        $persenGuru    = isset($pengaturan['persen_admin_guru']) ? (float)$pengaturan['persen_admin_guru'] : 1.0;
        $persenSekolah = isset($pengaturan['persen_admin_sekolah']) ? (float)$pengaturan['persen_admin_sekolah'] : 1.5;

        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $data = [
            'title'           => 'Laporan Tabungan',
            'jenisLaporan'    => $jenisLaporan,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'includeAlokasi'  => $includeAlokasi,
            'persenGuru'      => $persenGuru,
            'persenSekolah'   => $persenSekolah,
            'pengaturan'      => $pengaturan,
            'listSiswa'       => $siswaModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'listKelas'       => $kelasModel->orderBy('nama_kelas', 'ASC')->findAll(),
            'tahunAjaran'     => $tahunAjaranList,
            'selectedTahunId' => $selectedTahunId,
            'tahunAktif'      => $tahunAktif,
            'reportData'      => null,
            'reportDetails'   => null
        ];

        if ($jenisLaporan) {
            switch ($jenisLaporan) {
                case 'per_siswa':
                    $siswaId = $this->request->getGet('siswa_id');
                    if ($siswaId) {
                        $data['reportData'] = $transaksiModel->getLaporanPerSiswa($siswaId, $startDate, $endDate);
                        $data['selectedSiswa'] = $siswaModel->find($siswaId);
                    }
                    break;

                case 'per_kelas':
                    $kelasId = $this->request->getGet('kelas_id');
                    if ($kelasId) {
                        $riwayatKelasModel = new RiwayatKelasSiswa();
                        $targetTahun = $selectedTahunId ?: ($tahunAktif['id'] ?? null);
                        $data['reportData'] = [];
                        if ($targetTahun) {
                            $data['reportData'] = $riwayatKelasModel->getSiswaByKelasTahun($kelasId, $targetTahun);
                        }
                        $data['selectedKelas'] = $kelasModel->find($kelasId);
                    }
                    break;

                case 'pemasukan':
                    $data['reportData'] = $alokasiModel->getLaporanPemasukan($startDate, $endDate);
                    $data['reportDetails'] = $alokasiModel->getDetailPemasukan($startDate, $endDate);
                    break;
            }
        }

        return view('laporan/index', $data);
    }

    /**
     * Export Laporan ke Native Microsoft Excel (.xls)
     */
    public function exportExcel()
    {
        $jenisLaporan   = $this->request->getGet('jenis_laporan');
        $startDate      = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate        = $this->request->getGet('end_date') ?: date('Y-m-t');
        $includeAlokasi = (int) $this->request->getGet('include_alokasi');

        $siswaModel       = new Siswa();
        $kelasModel       = new Kelas();
        $transaksiModel   = new Transaksi();
        $pengaturanModel  = new \App\Models\Pengaturan();
        $pengaturan       = $pengaturanModel->getPengaturanAsArray();

        $persenGuru    = isset($pengaturan['persen_admin_guru']) ? (float)$pengaturan['persen_admin_guru'] : 1.0;
        $persenSekolah = isset($pengaturan['persen_admin_sekolah']) ? (float)$pengaturan['persen_admin_sekolah'] : 1.5;

        $filename = "laporan_{$jenisLaporan}_" . date('Ymd_His') . ".xls";
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo "<html><head><meta charset='utf-8'></head><body>";

        if ($jenisLaporan == 'per_siswa') {
            $siswaId = $this->request->getGet('siswa_id');
            $siswa   = $siswaModel->find($siswaId);
            $rows    = $transaksiModel->getLaporanPerSiswa($siswaId, $startDate, $endDate);

            echo "<h3 style='font-family:sans-serif;'>LAPORAN TABUNGAN PER SISWA</h3>";
            echo "<p style='font-family:sans-serif;'>Nama: <strong>" . esc($siswa['nama_lengkap'] ?? '-') . "</strong> | NIS: <strong>" . esc($siswa['nis'] ?? '-') . "</strong> | Periode: " . esc($startDate) . " s/d " . esc($endDate) . "</p>";
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse; font-family:sans-serif; font-size:12px;'>";
            echo "<tr style='background-color:#0277BD; color:#FFFFFF; font-weight:bold; text-align:center;'>";
            echo "<th>No</th><th>Kode Transaksi</th><th>Tanggal</th><th>Jenis Transaksi</th><th>Nominal (Rp)</th>";
            if ($includeAlokasi) {
                echo "<th>Alokasi Guru ({$persenGuru}%)</th><th>Alokasi Sekolah ({$persenSekolah}%)</th>";
            }
            echo "<th>Saldo Sebelum</th><th>Saldo Sesudah</th><th>Keterangan</th>";
            echo "</tr>";

            foreach ($rows as $idx => $r) {
                $jml = $r['jumlah'];
                $alokGuru = ($r['jenis_transaksi'] == 'setor') ? ($jml * ($persenGuru / 100)) : 0;
                $alokSekolah = ($r['jenis_transaksi'] == 'setor') ? ($jml * ($persenSekolah / 100)) : 0;

                echo "<tr>";
                echo "<td align='center'>" . ($idx + 1) . "</td>";
                echo "<td align='center'>" . esc($r['kode_transaksi']) . "</td>";
                echo "<td align='center'>" . date('d-m-Y H:i', strtotime($r['tanggal_transaksi'] ?? $r['created_at'])) . "</td>";
                echo "<td align='center'>" . strtoupper(esc($r['jenis_transaksi'])) . "</td>";
                echo "<td align='right'>" . number_format($r['jumlah'], 0, ',', '.') . "</td>";
                if ($includeAlokasi) {
                    echo "<td align='right'>" . number_format($alokGuru, 0, ',', '.') . "</td>";
                    echo "<td align='right'>" . number_format($alokSekolah, 0, ',', '.') . "</td>";
                }
                echo "<td align='right'>" . number_format($r['saldo_sebelum'], 0, ',', '.') . "</td>";
                echo "<td align='right'>" . number_format($r['saldo_sesudah'], 0, ',', '.') . "</td>";
                echo "<td>" . esc($r['keterangan']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";

        } elseif ($jenisLaporan == 'per_kelas') {
            $kelasId = $this->request->getGet('kelas_id');
            $kelas   = $kelasModel->find($kelasId);

            $tahunAjaranModel  = new TahunAjaran();
            $riwayatKelasModel = new RiwayatKelasSiswa();
            $selectedTahunId   = $this->request->getGet('tahun_ajaran_id');
            $tahunAktif        = $tahunAjaranModel->where('status', 'aktif')->first();
            $targetTahun       = $selectedTahunId ?: ($tahunAktif['id'] ?? null);

            $rows = [];
            if ($targetTahun && $kelasId) {
                $rows = $riwayatKelasModel->getSiswaByKelasTahun($kelasId, $targetTahun);
            }

            echo "<h3 style='font-family:sans-serif;'>REKAPITULASI TABUNGAN KELAS</h3>";
            echo "<p style='font-family:sans-serif;'>Kelas: <strong>" . esc($kelas['nama_kelas'] ?? '-') . "</strong></p>";
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse; font-family:sans-serif; font-size:12px;'>";
            echo "<tr style='background-color:#0277BD; color:#FFFFFF; font-weight:bold; text-align:center;'>";
            echo "<th>No</th><th>NIS</th><th>Nama Siswa</th><th>Saldo Tabungan (Rp)</th>";
            if ($includeAlokasi) {
                echo "<th>Alokasi Guru ({$persenGuru}%)</th><th>Alokasi Sekolah ({$persenSekolah}%)</th>";
            }
            echo "</tr>";

            $totalSaldo = 0;
            $totalAlokGuru = 0;
            $totalAlokSekolah = 0;
            foreach ($rows as $idx => $r) {
                $totalSaldo += $r['saldo_akhir'];
                $alokGuru = $r['saldo_akhir'] * ($persenGuru / 100);
                $alokSekolah = $r['saldo_akhir'] * ($persenSekolah / 100);
                $totalAlokGuru += $alokGuru;
                $totalAlokSekolah += $alokSekolah;

                echo "<tr>";
                echo "<td align='center'>" . ($idx + 1) . "</td>";
                echo "<td align='center'>'" . esc($r['nis']) . "</td>";
                echo "<td>" . esc($r['nama_lengkap']) . "</td>";
                echo "<td align='right'>" . number_format($r['saldo_akhir'], 0, ',', '.') . "</td>";
                if ($includeAlokasi) {
                    echo "<td align='right'>" . number_format($alokGuru, 0, ',', '.') . "</td>";
                    echo "<td align='right'>" . number_format($alokSekolah, 0, ',', '.') . "</td>";
                }
                echo "</tr>";
            }
            echo "<tr style='background-color:#F5F5F5; font-weight:bold;'>";
            echo "<td colspan='3' align='center'>TOTAL SALDO KELAS</td>";
            echo "<td align='right'>" . number_format($totalSaldo, 0, ',', '.') . "</td>";
            if ($includeAlokasi) {
                echo "<td align='right'>" . number_format($totalAlokGuru, 0, ',', '.') . "</td>";
                echo "<td align='right'>" . number_format($totalAlokSekolah, 0, ',', '.') . "</td>";
            }
            echo "</tr>";
            echo "</table>";
        }

        echo "</body></html>";
        exit;
    }
}
