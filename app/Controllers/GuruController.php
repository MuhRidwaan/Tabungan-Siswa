<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Guru;

class GuruController extends BaseController
{
    protected $guru;

    public function __construct()
    {
        $this->guru = new Guru();
    }

    /**
     * Menampilkan daftar guru
     */
    public function index()
    {
        $allUsers = $this->guru->findAll();
        $totalPengguna = count($allUsers);
        $totalGuru = 0;
        $totalAdmin = 0;

        foreach ($allUsers as $u) {
            if ($u['role'] == 'admin') {
                $totalAdmin++;
            } else {
                $totalGuru++;
            }
        }

        $data = [
            'title' => 'Manajemen Data Guru & Pengguna',
            'guru'  => $allUsers,
            'stats' => [
                'total_pengguna' => $totalPengguna,
                'total_guru'     => $totalGuru,
                'total_admin'    => $totalAdmin
            ]
        ];
        return view('guru/index', $data);
    }

    /**
     * Menampilkan form tambah guru
     */
    public function new()
    {
        $data = [
            'title'      => 'Tambah Data Guru',
            'validation' => \Config\Services::validation()
        ];
        return view('guru/create', $data);
    }

    /**
     * Menyimpan data guru baru
     */
    public function create()
    {
        $rules = [
            'nama_lengkap' => 'required|max_length[100]',
            'username'     => 'required|is_unique[pengguna.username]|max_length[50]',
            'password'     => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $this->guru->save([
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $username,
            'password'     => $password,
            'role'         => 'guru'
        ]);

        // Sync ke Shield Auth (users & auth_identities)
        $userModel = new \CodeIgniter\Shield\Models\UserModel();
        $existingShieldUser = $userModel->findByCredentials(['username' => $username]);
        if (!$existingShieldUser) {
            $guruUser = new \CodeIgniter\Shield\Entities\User([
                'username' => $username,
                'email'    => $username . '@guru.com',
                'password' => $password,
            ]);
            $userModel->save($guruUser);
        }

        session()->setFlashdata('success', 'Data guru berhasil ditambahkan.');
        return redirect()->to('/guru');
    }

    /**
     * Menampilkan form edit guru
     */
    public function edit($id)
    {
        $data = [
            'title'      => 'Edit Data Guru',
            'validation' => \Config\Services::validation(),
            'guru'       => $this->guru->find($id)
        ];
        return view('guru/edit', $data);
    }

    /**
     * Mengupdate data guru
     */
    public function update($id)
    {
        $rules = [
            'nama_lengkap' => 'required|max_length[100]',
            'username'     => "required|is_unique[pengguna.username,id,{$id}]|max_length[50]"
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[1]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $oldGuru = $this->guru->find($id);
        $newUsername = $this->request->getPost('username');
        $newPassword = $this->request->getPost('password');

        $dataToUpdate = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $newUsername
        ];

        if ($newPassword) {
            $dataToUpdate['password'] = $newPassword;
        }

        $this->guru->update($id, $dataToUpdate);

        // Sync ke Shield Auth
        if ($oldGuru) {
            $userModel = new \CodeIgniter\Shield\Models\UserModel();
            $shieldUser = $userModel->findByCredentials(['username' => $oldGuru['username']]);
            if ($shieldUser) {
                $shieldUser->username = $newUsername;
                if ($newPassword) {
                    $shieldUser->password = $newPassword;
                }
                $userModel->save($shieldUser);
            }
        }

        session()->setFlashdata('success', 'Data guru berhasil diupdate.');
        return redirect()->to('/guru');
    }

    /**
     * Menghapus data guru
     */
    public function delete($id)
    {
        $guru = $this->guru->find($id);
        if ($guru) {
            $userModel = new \CodeIgniter\Shield\Models\UserModel();
            $shieldUser = $userModel->findByCredentials(['username' => $guru['username']]);
            if ($shieldUser) {
                $userModel->delete($shieldUser->id, true);
            }
            $this->guru->delete($id);
        }

        session()->setFlashdata('success', 'Data guru berhasil dihapus.');
        return redirect()->to('/guru');
    }
}
