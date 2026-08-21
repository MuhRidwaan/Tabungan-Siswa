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

        $data = [
            'title'           => 'Penempatan & Kenaikan Kelas',
            'tahun_ajaran'    => $this->tahunAjaranModel->orderBy('tahun_mulai', 'DESC')->findAll(),
            'kelas'           => $this->kelasModel->orderBy('tingkat', 'ASC')->findAll(),
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
}
