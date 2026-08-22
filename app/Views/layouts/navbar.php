  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('dashboard') ?>" class="nav-link"><i class="fas fa-home mr-1"></i> Dashboard</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      
      <!-- User Profile Dropdown Menu -->
      <?php 
        $user = function_exists('auth') ? auth()->user() : null;
        $namaUserNav = session()->get('nama_lengkap');
        
        if (!$namaUserNav && $user) {
            $guruModel = new \App\Models\Guru();
            $guru = $guruModel->where('username', $user->username)->first();
            $namaUserNav = $guru['nama_lengkap'] ?? $user->username;
            session()->set('nama_lengkap', $namaUserNav);
        }
        if (!$namaUserNav && $user) {
            $namaUserNav = $user->username;
        }

        $fotoNav = session()->get('foto_profil');
        if (!$fotoNav && $user) {
            $uploadDir = FCPATH . 'uploads/profile/';
            if (is_dir($uploadDir)) {
                $files = glob($uploadDir . 'user_' . $user->id . '_*');
                if (!empty($files)) {
                    usort($files, function($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    $fotoNav = basename($files[0]);
                    session()->set('foto_profil', $fotoNav);
                }
            }
        }

        $avatarNavUrl = ($fotoNav && file_exists(FCPATH . 'uploads/profile/' . $fotoNav)) 
            ? base_url('uploads/profile/' . $fotoNav) 
            : base_url('dist/img/user2-160x160.jpg');
      ?>
      
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
          <img src="<?= $avatarNavUrl ?>" class="user-image img-circle elevation-1 mr-2" style="width: 32px; height: 32px; object-fit: cover;" alt="User Image">
          <span class="d-none d-md-inline font-weight-bold"><?= esc($namaUserNav) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->
          <li class="user-header bg-primary text-center py-3">
            <img src="<?= $avatarNavUrl ?>" class="img-circle elevation-2" style="width: 80px; height: 80px; object-fit: cover;" alt="User Image">
            <p class="mt-2 mb-0 font-weight-bold"><?= esc($namaUserNav) ?></p>
            <small><?= esc($user->email ?? '') ?></small>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer d-flex justify-content-between p-2">
            <a href="<?= base_url('profile') ?>" class="btn btn-default btn-flat"><i class="fas fa-user-cog mr-1"></i> Profile Saya</a>
            <a href="<?= base_url('logout') ?>" class="btn btn-default btn-flat text-danger"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Layar Penuh">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->