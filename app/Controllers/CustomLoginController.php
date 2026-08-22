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

        // Set session profile data upon login
        $user = auth()->user();
        if ($user) {
            $guruModel = new \App\Models\Guru();
            $guru = $guruModel->where('username', $user->username)->first();
            $namaLengkap = $guru['nama_lengkap'] ?? $user->username;

            $fotoProfil = null;
            $uploadDir = FCPATH . 'uploads/profile/';
            if (is_dir($uploadDir)) {
                $files = glob($uploadDir . 'user_' . $user->id . '_*');
                if (!empty($files)) {
                    usort($files, function($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    $fotoProfil = basename($files[0]);
                }
            }

            session()->set([
                'nama_lengkap' => $namaLengkap,
                'username'     => $user->username,
                'email'        => $user->email,
                'foto_profil'  => $fotoProfil
            ]);
        }

        session()->removeTempdata('beforeLoginUrl');
        return redirect()->to(base_url('dashboard'))->withCookies();
    }
}
