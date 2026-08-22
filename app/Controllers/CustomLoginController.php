<?php

namespace App\Controllers;

use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;
use CodeIgniter\HTTP\RedirectResponse;

class CustomLoginController extends ShieldLoginController
{
    /**
     * Override loginAction to support login using either Email OR Username
     */
    public function loginAction(): RedirectResponse
    {
        $loginInput = trim((string)$this->request->getPost('email'));
        if (empty($loginInput)) {
            $loginInput = trim((string)$this->request->getPost('login'));
        }
        if (empty($loginInput)) {
            $loginInput = trim((string)$this->request->getPost('username'));
        }

        $password = (string)$this->request->getPost('password');
        $remember = (bool)$this->request->getPost('remember');

        if (empty($loginInput) || empty($password)) {
            return redirect()->route('login')->withInput()->with('error', 'Silakan masukkan Email/Username dan Password.');
        }

        $credentials = [
            'password' => $password
        ];

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginInput;
        } else {
            $credentials['username'] = $loginInput;
        }

        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // Attempt to login
        $result = $authenticator->remember($remember)->attempt($credentials);

        // Jika belum berhasil via username, coba cari user berdasar username di UserModel lalu login via emailnya
        if (! $result->isOK() && isset($credentials['username'])) {
            $userModel = new \CodeIgniter\Shield\Models\UserModel();
            $user = $userModel->where('username', $loginInput)->first();
            if ($user && $user->email) {
                $result = $authenticator->remember($remember)->attempt([
                    'email'    => $user->email,
                    'password' => $password
                ]);
            }
        }

        if (! $result->isOK()) {
            return redirect()->route('login')->withInput()->with('error', $result->reason());
        }

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show')->withCookies();
        }

        return redirect()->to(config('Auth')->loginRedirect())->withCookies();
    }

    /**
     * AJAX Lookup User Avatar & Profile Information before entering password
     */
    public function checkUserAvatar()
    {
        $db = \Config\Database::connect();
        $loginInput = trim((string)($this->request->getGet('login') ?: $this->request->getPost('login')));
        if (empty($loginInput)) {
            $loginInput = trim((string)($this->request->getGet('username') ?: $this->request->getPost('username')));
        }
        if (empty($loginInput)) {
            $loginInput = trim((string)($this->request->getGet('email') ?: $this->request->getPost('email')));
        }

        if (empty($loginInput)) {
            return $this->response->setJSON(['found' => false, 'message' => 'Username/Email kosong']);
        }

        $userModel = new \CodeIgniter\Shield\Models\UserModel();
        $user = null;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $identityTable = $db->table('auth_identities')
                                ->where('type', 'email_password')
                                ->where('secret', $loginInput)
                                ->get()
                                ->getRowArray();
            if ($identityTable) {
                $user = $userModel->find($identityTable['user_id']);
            }
        } else {
            $user = $userModel->where('username', $loginInput)->first();
        }

        if (!$user) {
            // Check Guru table directly as fallback
            $guruModel = new \App\Models\Guru();
            $guru = $guruModel->where('username', $loginInput)->orWhere('email', $loginInput)->first();
            if ($guru) {
                $fullName = $guru['nama_guru'] ?? $guru['username'];
                $role = 'Guru / Wali Kelas';
                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&background=6366f1&color=ffffff&size=128&bold=true";

                return $this->response->setJSON([
                    'found'     => true,
                    'username'  => $guru['username'],
                    'full_name' => $fullName,
                    'role'      => $role,
                    'avatar'    => $avatarUrl
                ]);
            }

            return $this->response->setJSON(['found' => false]);
        }

        // Get User details
        $username  = $user->username;
        $guruModel = new \App\Models\Guru();
        $guru      = $guruModel->where('username', $username)->first();

        $fullName = $guru['nama_lengkap'] ?? ($guru['nama_guru'] ?? ($username ?: 'Pengguna Tabungan'));
        $groups   = $user->getGroups();
        $roleName = !empty($groups) ? ucfirst($groups[0]) : 'Pengelola Tabungan';
        if (in_array('admin', $groups)) {
            $roleName = 'Administrator Sekolah';
        }

        // Check custom uploaded photo in uploads/profile/
        $avatarUrl = null;
        $hasCustomPhoto = false;
        $uploadDir = FCPATH . 'uploads/profile/';
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . 'user_' . $user->id . '_*');
            if (!empty($files)) {
                // Sort files by newest modified time
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $avatarUrl = base_url('uploads/profile/' . basename($files[0]));
                $hasCustomPhoto = true;
            }
        }

        if (!$avatarUrl) {
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&background=4f46e5&color=ffffff&size=128&bold=true";
        }

        return $this->response->setJSON([
            'found'            => true,
            'username'         => $username,
            'full_name'        => $fullName,
            'role'             => $roleName,
            'avatar'           => $avatarUrl,
            'has_custom_photo' => $hasCustomPhoto
        ]);
    }
}
