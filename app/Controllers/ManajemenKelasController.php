<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Siswa; // Sesuaikan dengan nama model Anda
use App\Models\RiwayatKelasSiswa;

class ManajemenKelasController extends BaseController
{
    protected $tahunAjaranModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $riwayatKelasModel;

    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaran();
        $this->kelasModel = new Kelas();
        $this->siswaModel = new Siswa();
        $this->riwayatKelasModel = new RiwayatKelasSiswa();
    }

    public function index()
    {
        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');
        $selectedKelasId = $this->request->getGet('kelas_id');

        $kelasQuery = $this->kelasModel->orderBy('tingkat', 'ASC');
        if ($selectedTahunId) {
            $kelasQuery->where('tahun_ajaran_id', $selectedTahunId);
        }

        $data = [
            'title'           => 'Penempatan & Kenaikan Kelas',
            'tahun_ajaran'    => $this->tahunAjaranModel->orderBy('tahun_mulai', 'DESC')->findAll(),
            'kelas'           => $kelasQuery->findAll(),
            'selectedTahunId' => $selectedTahunId,
            'selectedKelasId' => $selectedKelasId,
            'siswaDiKelas'    => [],
            'siswaLuarKelas'  => [],
        ];

        if ($selectedTahunId) {
            // Ambil daftar siswa yang belum ditempatkan di tahun ajaran ini
            $db = \Config\Database::connect();
            $builder = $db->table('siswa');
            $builder->select('siswa.id, siswa.nis, siswa.nama_lengkap');
            $builder->where('siswa.status_siswa', 'aktif');
            $builder->where("NOT EXISTS (
                SELECT 1 FROM riwayat_kelas_siswa 
                WHERE riwayat_kelas_siswa.siswa_id = siswa.id 
                AND riwayat_kelas_siswa.tahun_ajaran_id = " . $db->escape($selectedTahunId) . "
            )");
            $data['siswaLuarKelas'] = $builder->get()->getResultArray();

            // Jika kelas juga dipilih, ambil daftar siswa di kelas itu
            if ($selectedKelasId) {
                $data['siswaDiKelas'] = $this->riwayatKelasModel->getSiswaByKelasTahun($selectedKelasId, $selectedTahunId);
            }
        }

        return view('manajemen_kelas/index', $data);
    }

    /**
     * Proses untuk memasukkan siswa ke dalam kelas
     */
    public function assign()
    {
        $siswaIds = $this->request->getPost('siswa_ids');
        $kelasId = $this->request->getPost('kelas_id');
        $tahunAjaranId = $this->request->getPost('tahun_ajaran_id');

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih.');
        }

        foreach ($siswaIds as $siswaId) {
            $this->riwayatKelasModel->insert([
                'siswa_id'        => $siswaId,
                'kelas_id'        => $kelasId,
                'tahun_ajaran_id' => $tahunAjaranId,
            ]);
        }

        session()->setFlashdata('success', count($siswaIds) . ' siswa berhasil ditempatkan.');
        return redirect()->to('/manajemen-kelas?tahun_ajaran_id=' . $tahunAjaranId . '&kelas_id=' . $kelasId);
    }

    /**
     * Proses untuk mengeluarkan siswa dari kelas
     */
    public function unassign($riwayatId)
    {
        $riwayat = $this->riwayatKelasModel->find($riwayatId);
        if ($riwayat) {
            $this->riwayatKelasModel->delete($riwayatId);
            session()->setFlashdata('success', 'Siswa berhasil dikeluarkan dari kelas.');
            return redirect()->to('/manajemen-kelas?tahun_ajaran_id=' . $riwayat['tahun_ajaran_id'] . '&kelas_id=' . $riwayat['kelas_id']);
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    /**
     * Proses Kenaikan Kelas Massal Interaktif
     */
    public function promote()
    {
        $tahunLamaId  = $this->request->getPost('tahun_ajaran_lama_id');
        $kelasAsalId   = $this->request->getPost('kelas_asal_id');
        $tahunBaruId  = $this->request->getPost('tahun_ajaran_baru_id');
        $kelasTujuanId = $this->request->getPost('kelas_tujuan_id');
        $siswaIds     = $this->request->getPost('siswa_ids') ?: [];
        $tinggalIds   = $this->request->getPost('siswa_tinggal_ids') ?: [];

        if (!$tahunLamaId || !$kelasAsalId || !$tahunBaruId || !$kelasTujuanId) {
            return redirect()->back()->with('error', 'Silakan lengkapi parameter Tahun Ajaran dan Kelas Asal/Tujuan.');
        }

        if (empty($siswaIds) && empty($tinggalIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih untuk diproses.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $countPromoted = 0;

        foreach ($siswaIds as $sid) {
            if ($kelasTujuanId === 'lulus') {
                $this->siswaModel->update($sid, ['status_siswa' => 'lulus']);
                $countPromoted++;
            } else {
                $existing = $this->riwayatKelasModel->where('siswa_id', $sid)
                                                    ->where('tahun_ajaran_id', $tahunBaruId)
                                                    ->first();
                if ($existing) {
                    $this->riwayatKelasModel->update($existing['id'], ['kelas_id' => $kelasTujuanId]);
                } else {
                    $this->riwayatKelasModel->insert([
                        'siswa_id'        => $sid,
                        'kelas_id'        => $kelasTujuanId,
                        'tahun_ajaran_id' => $tahunBaruId
                    ]);
                }
                $countPromoted++;
            }
        }

        foreach ($tinggalIds as $sid) {
            $existing = $this->riwayatKelasModel->where('siswa_id', $sid)
                                                ->where('tahun_ajaran_id', $tahunBaruId)
                                                ->first();
            if ($existing) {
                $this->riwayatKelasModel->update($existing['id'], ['kelas_id' => $kelasAsalId]);
            } else {
                $this->riwayatKelasModel->insert([
                    'siswa_id'        => $sid,
                    'kelas_id'        => $kelasAsalId,
                    'tahun_ajaran_id' => $tahunBaruId
                ]);
            }
        }

        $db->transComplete();

        session()->setFlashdata('success', "Proses Kenaikan Kelas Selesai: {$countPromoted} siswa berhasil diproses ke Tahun Ajaran Baru!");
        return redirect()->to('/manajemen-kelas?tahun_ajaran_id=' . $tahunBaruId . '&kelas_id=' . ($kelasTujuanId === 'lulus' ? $kelasAsalId : $kelasTujuanId));
    }
}
