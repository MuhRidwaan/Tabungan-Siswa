<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><?= esc($title) ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Active Academic Year Alert -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <i class="fas fa-calendar-check mr-2"></i>
      <strong>Tahun Ajaran Aktif:</strong> <?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Belum Diatur') ?>
    </div>

    <!-- Small boxes (Stat box) -->
    <div class="row">
      <!-- Total Siswa -->
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?= number_format($totalSiswa, 0, ',', '.') ?></h3>
            <p>Total Siswa Aktif</p>
          </div>
          <div class="icon">
            <i class="fas fa-users"></i>
          </div>
          <a href="<?= base_url('siswa') ?>" class="small-box-footer">Lihat Siswa <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- Total Saldo Tabungan -->
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>Rp <?= number_format($totalSaldo, 0, ',', '.') ?></h3>
            <p>Total Saldo Tabungan</p>
          </div>
          <div class="icon">
            <i class="fas fa-wallet"></i>
          </div>
          <a href="<?= base_url('transaksi') ?>" class="small-box-footer">Lihat Transaksi <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- Setoran Bulan Ini -->
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>Rp <?= number_format($totalSetorBulanIni, 0, ',', '.') ?></h3>
            <p>Setoran Bulan Ini</p>
          </div>
          <div class="icon">
            <i class="fas fa-arrow-down text-dark"></i>
          </div>
          <a href="<?= base_url('laporan?jenis_laporan=pemasukan') ?>" class="small-box-footer">Laporan Kas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- Penarikan Bulan Ini -->
      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>Rp <?= number_format($totalTarikBulanIni, 0, ',', '.') ?></h3>
            <p>Penarikan Bulan Ini</p>
          </div>
          <div class="icon">
            <i class="fas fa-arrow-up"></i>
          </div>
          <a href="<?= base_url('transaksi') ?>" class="small-box-footer">Detail Transaksi <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="row mt-3">
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-history mr-1"></i> Transaksi Terakhir
            </h3>
            <div class="card-tools">
              <a href="<?= base_url('transaksi') ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-list"></i> Lihat Semua
              </a>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead>
                  <tr>
                    <th>Waktu</th>
                    <th>Kode</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jenis</th>
                    <th class="text-right">Jumlah</th>
                    <th>Petugas</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($recentTransaksi)) : ?>
                    <?php foreach ($recentTransaksi as $t) : ?>
                      <tr>
                        <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                        <td><span class="badge badge-secondary"><?= esc($t['kode_transaksi']) ?></span></td>
                        <td><?= esc($t['nis']) ?></td>
                        <td><?= esc($t['nama_siswa']) ?></td>
                        <td>
                          <?php if ($t['jenis_transaksi'] == 'setor') : ?>
                            <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Setor</span>
                          <?php else : ?>
                            <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Tarik</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-right font-weight-bold">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                        <td><?= esc($t['nama_pengguna']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi tabungan.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?= $this->endSection() ?>
