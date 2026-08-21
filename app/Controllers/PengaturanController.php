<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Pengaturan; // Sesuaikan dengan nama model Anda

class PengaturanController extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new Pengaturan();
    }

    /**
     * Menampilkan halaman pengaturan
     */
    public function index()
    {
        $data = [
            'title'      => 'Pengaturan Aplikasi',
            'validation' => \Config\Services::validation(),
            'pengaturan' => $this->pengaturanModel->getPengaturanAsArray()
        ];
        return view('pengaturan/index', $data);
    }

    /**
     * Menyimpan perubahan pengaturan
     */
    public function update()
    {
        $rules = [
            'persen_admin_guru'   => 'required|decimal',
            'persen_admin_sekolah' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $allowed = ['persen_admin_guru', 'persen_admin_sekolah'];
        foreach ($allowed as $nama) {
            $nilai = $this->request->getPost($nama);
            if ($nilai !== null) {
                $this->pengaturanModel->where('nama_pengaturan', $nama)
                                      ->set('nilai_pengaturan', $nilai)
                                      ->update();
            }
        }

        session()->setFlashdata('success', 'Pengaturan berhasil diperbarui.');
        return redirect()->to('/pengaturan');
    }
}
