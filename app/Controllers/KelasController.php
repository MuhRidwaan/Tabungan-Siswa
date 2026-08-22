<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Kelas;
use App\Models\Guru; // Kita butuh ini untuk mengambil daftar guru

class KelasController extends BaseController
{
    protected $kelas;
    protected $guru;

    public function __construct()
    {
        $this->kelas = new Kelas();
        $this->guru = new Guru();
    }

    /**
     * Menampilkan daftar kelas
     */
    public function index()
    {
        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $tahunAjaranList  = $tahunAjaranModel->orderBy('id', 'DESC')->findAll();
        $tahunAktif       = $tahunAjaranModel->where('status', 'aktif')->first();

        $selectedTahunId  = $this->request->getGet('tahun_ajaran_id');
        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $kelases = $this->kelas->getAllKelasWithWali($selectedTahunId);

        $totalKelas = count($kelases);
        $totalWali  = 0;
        $totalSiswa = 0;

        foreach ($kelases as $k) {
            if (!empty($k['wali_kelas_id'])) {
                $totalWali++;
            }
            $totalSiswa += (int) ($k['total_siswa'] ?? 0);
        }

        $data = [
            'title'           => 'Manajemen Data Kelas',
            'kelas'           => $kelases,
            'tahunAktif'      => $tahunAktif,
            'tahunAjaran'     => $tahunAjaranList,
            'selectedTahunId' => $selectedTahunId,
            'stats'           => [
                'total_kelas' => $totalKelas,
                'total_wali'  => $totalWali,
                'total_siswa' => $totalSiswa
            ]
        ];
        return view('kelas/index', $data);
    }

    /**
     * Menampilkan form tambah kelas
     */
    public function new()
    {
        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $selectedTahunId  = $this->request->getGet('tahun_ajaran_id');
        $tahunAktif       = $tahunAjaranModel->where('status', 'aktif')->first();

        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $data = [
            'title'           => 'Tambah Data Kelas Baru',
            'validation'      => \Config\Services::validation(),
            'guru'            => $this->guru->where('role', 'guru')->findAll(),
            'tahunAjaran'     => $tahunAjaranModel->orderBy('id', 'DESC')->findAll(),
            'selectedTahunId' => $selectedTahunId
        ];
        return view('kelas/create', $data);
    }

    /**
     * Menyimpan data kelas baru
     */
    public function create()
    {
        $tahunAjaranId = $this->request->getPost('tahun_ajaran_id');
        $namaKelas     = trim((string)$this->request->getPost('nama_kelas'));

        $rules = [
            'tahun_ajaran_id' => 'required|numeric',
            'nama_kelas'      => 'required|max_length[50]',
            'tingkat'         => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Cek keunikan nama_kelas khusus dalam tahun_ajaran_id yang sama
        $exists = $this->kelas->where('nama_kelas', $namaKelas)
                             ->where('tahun_ajaran_id', $tahunAjaranId)
                             ->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Nama kelas "' . esc($namaKelas) . '" sudah ada pada Tahun Ajaran tersebut.');
        }

        $this->kelas->save([
            'nama_kelas'      => $namaKelas,
            'tingkat'         => $this->request->getPost('tingkat'),
            'wali_kelas_id'   => $this->request->getPost('wali_kelas_id') ?: null,
            'tahun_ajaran_id' => $tahunAjaranId
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil ditambahkan.');
        return redirect()->to('/kelas?tahun_ajaran_id=' . $tahunAjaranId);
    }

    /**
     * Menampilkan form edit kelas
     */
    public function edit($id)
    {
        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $data = [
            'title'       => 'Edit Data Kelas',
            'validation'  => \Config\Services::validation(),
            'kelas'       => $this->kelas->find($id),
            'guru'        => $this->guru->where('role', 'guru')->findAll(),
            'tahunAjaran' => $tahunAjaranModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('kelas/edit', $data);
    }

    /**
     * Mengupdate data kelas
     */
    public function update($id)
    {
        $tahunAjaranId = $this->request->getPost('tahun_ajaran_id');
        $namaKelas     = trim((string)$this->request->getPost('nama_kelas'));

        $rules = [
            'tahun_ajaran_id' => 'required|numeric',
            'nama_kelas'      => 'required|max_length[50]',
            'tingkat'         => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $exists = $this->kelas->where('nama_kelas', $namaKelas)
                             ->where('tahun_ajaran_id', $tahunAjaranId)
                             ->where('id !=', $id)
                             ->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Nama kelas "' . esc($namaKelas) . '" sudah ada pada Tahun Ajaran tersebut.');
        }

        $this->kelas->update($id, [
            'nama_kelas'      => $namaKelas,
            'tingkat'         => $this->request->getPost('tingkat'),
            'wali_kelas_id'   => $this->request->getPost('wali_kelas_id') ?: null,
            'tahun_ajaran_id' => $tahunAjaranId
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil diupdate.');
        return redirect()->to('/kelas?tahun_ajaran_id=' . $tahunAjaranId);
    }

    /**
     * Menghapus data kelas
     */
    public function delete($id)
    {
        $riwayatModel = new \App\Models\RiwayatKelasSiswa();
        $countSiswa = $riwayatModel->where('kelas_id', $id)->countAllResults();

        if ($countSiswa > 0) {
            session()->setFlashdata('error', "Gagal menghapus! Kelas ini masih terdaftar pada {$countSiswa} riwayat penempatan siswa. Silakan keluarkan atau pindahkan siswa terlebih dahulu.");
            return redirect()->to('/kelas');
        }

        try {
            $this->kelas->delete($id);
            session()->setFlashdata('success', 'Data kelas berhasil dihapus.');
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Gagal menghapus kelas karena data ini masih digunakan oleh data lain di sistem.');
        }

        return redirect()->to('/kelas');
    }
}
