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
        $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();
        $tahunId = $tahunAktif['id'] ?? null;

        $kelases = $this->kelas->getAllKelasWithWali($tahunId);

        $totalKelas = count($kelases);
        $totalWali = 0;
        $totalSiswa = 0;

        foreach ($kelases as $k) {
            if (!empty($k['wali_kelas_id'])) {
                $totalWali++;
            }
            $totalSiswa += (int) ($k['total_siswa'] ?? 0);
        }

        $data = [
            'title'      => 'Manajemen Data Kelas',
            'kelas'      => $kelases,
            'tahunAktif' => $tahunAktif,
            'stats'      => [
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
        $data = [
            'title'      => 'Tambah Data Kelas',
            'validation' => \Config\Services::validation(),
            'guru'       => $this->guru->where('role', 'guru')->findAll() // Ambil daftar guru
        ];
        return view('kelas/create', $data);
    }

    /**
     * Menyimpan data kelas baru
     */
    public function create()
    {
        $rules = [
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas]|max_length[50]',
            'tingkat'    => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->kelas->save([
            'nama_kelas'    => $this->request->getPost('nama_kelas'),
            'tingkat'       => $this->request->getPost('tingkat'),
            'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null, // Set NULL jika tidak dipilih
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil ditambahkan.');
        return redirect()->to('/kelas');
    }

    /**
     * Menampilkan form edit kelas
     */
    public function edit($id)
    {
        $data = [
            'title'      => 'Edit Data Kelas',
            'validation' => \Config\Services::validation(),
            'kelas'      => $this->kelas->find($id),
            'guru'       => $this->guru->where('role', 'guru')->findAll()
        ];
        return view('kelas/edit', $data);
    }

    /**
     * Mengupdate data kelas
     */
    public function update($id)
    {
        $rules = [
            'nama_kelas' => "required|is_unique[kelas.nama_kelas,id,{$id}]|max_length[50]",
            'tingkat'    => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->kelas->update($id, [
            'nama_kelas'    => $this->request->getPost('nama_kelas'),
            'tingkat'       => $this->request->getPost('tingkat'),
            'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null,
        ]);

        session()->setFlashdata('success', 'Data kelas berhasil diupdate.');
        return redirect()->to('/kelas');
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
