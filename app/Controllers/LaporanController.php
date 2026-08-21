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
}
