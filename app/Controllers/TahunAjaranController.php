<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TahunAjaran; // Sesuaikan dengan nama model Anda

class TahunAjaranController extends BaseController
{
    protected $tahunAjaranModel;

    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaran();
    }

    /**
     * Menampilkan daftar tahun ajaran
     */
    public function index()
    {
        $list = $this->tahunAjaranModel->orderBy('tahun_mulai', 'DESC')->findAll();
        $totalTa = count($list);
        $taAktif = null;

        foreach ($list as $t) {
            if ($t['status'] == 'aktif') {
                $taAktif = $t['nama_tahun_ajaran'];
                break;
            }
        }

        $data = [
            'title'        => 'Manajemen Data Tahun Ajaran',
            'tahun_ajaran' => $list,
            'stats'        => [
                'total_ta' => $totalTa,
                'ta_aktif' => $taAktif ?? 'Belum Diatur'
            ]
        ];
        return view('tahun_ajaran/index', $data);
    }

    /**
     * Set 1-klik Tahun Ajaran Aktif
     */
    public function setActive($id)
    {
        $this->nonaktifkanSemuaTahunAjaran();
        $this->tahunAjaranModel->update($id, ['status' => 'aktif']);

        session()->setFlashdata('success', 'Tahun ajaran berhasil diaktifkan.');
        return redirect()->to('/tahun-ajaran');
    }

    /**
     * Menampilkan form tambah tahun ajaran
     */
    public function new()
    {
        $data = [
            'title'      => 'Tambah Tahun Ajaran',
            'validation' => \Config\Services::validation()
        ];
        return view('tahun_ajaran/create', $data);
    }

    /**
     * Menyimpan data tahun ajaran baru
     */
    public function create()
    {
        $rules = [
            'tahun_mulai' => 'required|integer|exact_length[4]',
            'tahun_selesai' => 'required|integer|exact_length[4]',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Jika status yang diinput 'aktif', nonaktifkan semua tahun ajaran lainnya
        if ($this->request->getPost('status') == 'aktif') {
            $this->nonaktifkanSemuaTahunAjaran();
        }

        $this->tahunAjaranModel->save([
            'tahun_mulai'       => $this->request->getPost('tahun_mulai'),
            'tahun_selesai'     => $this->request->getPost('tahun_selesai'),
            'nama_tahun_ajaran' => $this->request->getPost('tahun_mulai') . '/' . $this->request->getPost('tahun_selesai'),
            'status'            => $this->request->getPost('status'),
        ]);

        session()->setFlashdata('success', 'Tahun ajaran berhasil ditambahkan.');
        return redirect()->to('/tahun-ajaran');
    }

    /**
     * Menampilkan form edit tahun ajaran
     */
    public function edit($id)
    {
        $data = [
            'title'        => 'Edit Tahun Ajaran',
            'validation'   => \Config\Services::validation(),
            'tahun_ajaran' => $this->tahunAjaranModel->find($id)
        ];
        return view('tahun_ajaran/edit', $data);
    }

    /**
     * Mengupdate data tahun ajaran
     */
    public function update($id)
    {
        $rules = [
            'tahun_mulai' => 'required|integer|exact_length[4]',
            'tahun_selesai' => 'required|integer|exact_length[4]',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Jika status yang diinput 'aktif', nonaktifkan semua tahun ajaran lainnya
        if ($this->request->getPost('status') == 'aktif') {
            $this->nonaktifkanSemuaTahunAjaran($id); // Beri pengecualian untuk ID saat ini
        }

        $this->tahunAjaranModel->update($id, [
            'tahun_mulai'       => $this->request->getPost('tahun_mulai'),
            'tahun_selesai'     => $this->request->getPost('tahun_selesai'),
            'nama_tahun_ajaran' => $this->request->getPost('tahun_mulai') . '/' . $this->request->getPost('tahun_selesai'),
            'status'            => $this->request->getPost('status'),
        ]);

        session()->setFlashdata('success', 'Tahun ajaran berhasil diupdate.');
        return redirect()->to('/tahun-ajaran');
    }

    /**
     * Menghapus data tahun ajaran
     */
    public function delete($id)
    {
        $this->tahunAjaranModel->delete($id);
        session()->setFlashdata('success', 'Tahun ajaran berhasil dihapus.');
        return redirect()->to('/tahun-ajaran');
    }

    /**
     * Helper function untuk menonaktifkan semua tahun ajaran
     */
    private function nonaktifkanSemuaTahunAjaran($exceptId = null)
    {
        $builder = $this->tahunAjaranModel->builder();
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        $builder->update(['status' => 'tidak aktif']);
    }
}
