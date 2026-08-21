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
        $jenisLaporan = $this->request->getGet('jenis_laporan');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        // Inisialisasi Models
        $siswaModel = new Siswa();
        $kelasModel = new Kelas();
        $transaksiModel = new Transaksi();
        $alokasiModel = new AlokasiBiayaAdmin();

        $data = [
            'title'         => 'Laporan Tabungan',
            'jenisLaporan'  => $jenisLaporan,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'listSiswa'     => $siswaModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'listKelas'     => $kelasModel->orderBy('nama_kelas', 'ASC')->findAll(),
            'reportData'    => null,
            'reportDetails' => null
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
                        $tahunAjaranModel = new TahunAjaran();
                        $riwayatKelasModel = new RiwayatKelasSiswa();
                        
                        $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();
                        $data['reportData'] = [];
                        if ($tahunAktif) {
                            $data['reportData'] = $riwayatKelasModel->getSiswaByKelasTahun($kelasId, $tahunAktif['id']);
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
     * Export Laporan ke CSV/Excel
     */
    public function exportExcel()
    {
        $jenisLaporan = $this->request->getGet('jenis_laporan');
        $startDate    = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate      = $this->request->getGet('end_date') ?: date('Y-m-t');

        $siswaModel     = new Siswa();
        $kelasModel     = new Kelas();
        $transaksiModel = new Transaksi();

        $filename = "laporan_{$jenisLaporan}_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');

        if ($jenisLaporan == 'per_siswa') {
            $siswaId = $this->request->getGet('siswa_id');
            $siswa   = $siswaModel->find($siswaId);
            $rows    = $transaksiModel->getLaporanPerSiswa($siswaId, $startDate, $endDate);

            fputcsv($output, ['LAPORAN TABUNGAN PER SISWA']);
            fputcsv($output, ['Nama Siswa', $siswa['nama_lengkap'] ?? '-']);
            fputcsv($output, ['NIS', $siswa['nis'] ?? '-']);
            fputcsv($output, ['Periode', "{$startDate} s/d {$endDate}"]);
            fputcsv($output, []);
            fputcsv($output, ['No', 'Kode Transaksi', 'Tanggal', 'Jenis Transaksi', 'Nominal (Rp)', 'Saldo Sebelum', 'Saldo Sesudah', 'Keterangan']);

            foreach ($rows as $idx => $r) {
                fputcsv($output, [
                    $idx + 1,
                    $r['kode_transaksi'],
                    $r['created_at'],
                    strtoupper($r['jenis_transaksi']),
                    number_format($r['jumlah'], 0, ',', '.'),
                    number_format($r['saldo_sebelum'], 0, ',', '.'),
                    number_format($r['saldo_sesudah'], 0, ',', '.'),
                    $r['keterangan']
                ]);
            }
        } elseif ($jenisLaporan == 'per_kelas') {
            $kelasId = $this->request->getGet('kelas_id');
            $kelas   = $kelasModel->find($kelasId);

            $tahunAjaranModel  = new TahunAjaran();
            $riwayatKelasModel = new RiwayatKelasSiswa();
            $tahunAktif        = $tahunAjaranModel->where('status', 'aktif')->first();
            $rows              = [];
            if ($tahunAktif && $kelasId) {
                $rows = $riwayatKelasModel->getSiswaByKelasTahun($kelasId, $tahunAktif['id']);
            }

            fputcsv($output, ['REKAPITULASI TABUNGAN KELAS']);
            fputcsv($output, ['Kelas', $kelas['nama_kelas'] ?? '-']);
            fputcsv($output, ['Tahun Ajaran', $tahunAktif['nama_tahun_ajaran'] ?? '-']);
            fputcsv($output, []);
            fputcsv($output, ['No', 'NIS', 'Nama Siswa', 'Saldo Tabungan (Rp)']);

            $totalSaldo = 0;
            foreach ($rows as $idx => $r) {
                $totalSaldo += $r['saldo_akhir'];
                fputcsv($output, [
                    $idx + 1,
                    $r['nis'],
                    $r['nama_lengkap'],
                    number_format($r['saldo_akhir'], 0, ',', '.')
                ]);
            }
            fputcsv($output, ['', '', 'TOTAL SALDO KELAS', number_format($totalSaldo, 0, ',', '.')]);
        }

        fclose($output);
        exit;
    }
}
