<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\Guru;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new Guru();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman edit profile user
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $data = [
            'title'      => 'Pengaturan Profile Saya',
            'user'       => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('profile/index', $data);
    }

    /**
     * Meng-update profile & password user
     */
    public function update()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $userId = $user->id;

        $rules = [
            'username'     => "required|max_length[100]|is_unique[users.username,id,{$userId}]",
            'email'        => "required|valid_email|max_length[254]",
            'nama_lengkap' => "required|max_length[100]"
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[4]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username    = trim($this->request->getPost('username'));
        $email       = trim($this->request->getPost('email'));
        $namaLengkap = trim($this->request->getPost('nama_lengkap'));

        $this->db->transStart();

        // 1. Update data dasar pada tabel users (Shield)
        $this->userModel->update($userId, [
            'username' => $username,
        ]);

        // 2. Update email pada tabel auth_identities (Shield)
        $identity = $this->db->table('auth_identities')
                             ->where('user_id', $userId)
                             ->where('type', 'email_password')
                             ->get()
                             ->getRowArray();

        if ($identity) {
            $updateData = ['secret' => $email];
            if (!empty($password)) {
                $passwordsService = service('passwords');
                $updateData['secret2'] = $passwordsService->hash($password);
            }
            $this->db->table('auth_identities')
                     ->where('id', $identity['id'])
                     ->update($updateData);
        }

        // 3. Sync dengan tabel guru jika user adalah guru
        $guru = $this->guruModel->where('email', $user->email)->orWhere('username', $user->username)->first();
        if ($guru) {
            $guruData = [
                'nama_guru' => $namaLengkap,
                'username'  => $username,
                'email'     => $email
            ];
            if (!empty($password)) {
                $guruData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $this->guruModel->update($guru['id'], $guruData);
        }

        $this->db->transComplete();

        // Update data session
        session()->set([
            'nama_lengkap' => $namaLengkap,
            'username'     => $username,
            'email'        => $email
        ]);

        session()->setFlashdata('success', 'Profil Anda berhasil diperbarui.');
        return redirect()->to('/profile');
    }
}
