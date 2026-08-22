<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 1. Shield Auth routes (login, register, logout, dsb)
service('auth')->routes($routes);
$routes->post('login', 'CustomLoginController::loginAction');

// 2. Arahkan '/' ke halaman dashboard
$routes->get('/', function () {
    return redirect()->to('dashboard');
});

// 3. Semua route setelah login
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Home::index');

    $routes->get ('profile',        'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');

    $routes->get ('siswa/download-template', 'SiswaController::downloadTemplate');
    $routes->get ('siswa/export',            'SiswaController::export');
    $routes->post('siswa/import',            'SiswaController::import');
    $routes->resource('siswa',        ['controller' => 'SiswaController']);
    $routes->resource('guru',         ['controller' => 'GuruController']);
    $routes->resource('kelas',        ['controller' => 'KelasController']);
    $routes->get ('tahun-ajaran/set-active/(:num)', 'TahunAjaranController::setActive/$1');
    $routes->resource('tahun-ajaran', ['controller' => 'TahunAjaranController']);

    $routes->get   ('manajemen-kelas',             'ManajemenKelasController::index');
    $routes->post  ('manajemen-kelas/assign',      'ManajemenKelasController::assign');
    $routes->post  ('manajemen-kelas/promote',     'ManajemenKelasController::promote');
    $routes->get   ('manajemen-kelas/unassign/(:num)', 'ManajemenKelasController::unassign/$1');

    $routes->get   ('transaksi',        'TransaksiController::index');
    $routes->get   ('transaksi/kolektif', 'TransaksiController::kolektif');
    $routes->get   ('transaksi/multi-tanggal', 'TransaksiController::multiTanggal');
    $routes->get   ('transaksi/akhir-tahun', 'TransaksiController::akhirTahun');
    $routes->get   ('transaksi/download-template-multi', 'TransaksiController::downloadTemplateMulti');
    $routes->post  ('transaksi/import-multi', 'TransaksiController::importMulti');
    $routes->get   ('transaksi/get-siswa-by-kelas', 'TransaksiController::getSiswaByKelas');
    $routes->get   ('transaksi/get-siswa-by-kelas/(:segment)', 'TransaksiController::getSiswaByKelas/$1');
    $routes->post  ('transaksi/save-kolektif', 'TransaksiController::saveKolektif');
    $routes->post  ('transaksi/save-multi-tanggal', 'TransaksiController::saveMultiTanggal');
    $routes->post  ('transaksi/save-tarik-lunas', 'TransaksiController::saveTarikLunas');
    $routes->post  ('transaksi/set-lulus-kelas', 'TransaksiController::setLulusKelas');
    $routes->post  ('transaksi/save',   'TransaksiController::save');
    $routes->delete('transaksi/(:num)', 'TransaksiController::delete/$1');

    $routes->get ('pengaturan',        'PengaturanController::index');
    $routes->post('pengaturan/update', 'PengaturanController::update');

    $routes->get('laporan',        'LaporanController::index');
    $routes->get('laporan/export', 'LaporanController::exportExcel');
});

// 4. Route khusus role "admin"
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get    ('roles',                 'RoleController::index');
    $routes->get    ('roles/create',          'RoleController::edit');
    $routes->get    ('roles/edit/(:segment)', 'RoleController::edit/$1');
    $routes->post   ('roles/save',            'RoleController::save');
    $routes->delete ('roles/(:segment)',      'RoleController::delete/$1');
});

$routes->setAutoRoute(false);