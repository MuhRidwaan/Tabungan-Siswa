<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-hand-holding-usd text-warning mr-2"></i><?= esc($title) ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('transaksi') ?>">Transaksi</a></li>
          <li class="breadcrumb-item active">Penarikan Akhir Tahun</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <!-- Filter Header Card -->
  <div class="card card-warning card-outline">
    <div class="card-header py-3">
      <h3 class="card-title font-weight-bold text-dark mb-0">
        <i class="fas fa-filter text-warning mr-2"></i>Filter Penarikan Tabungan Akhir Tahun Ajaran
      </h3>
    </div>
    <div class="card-body">
      <form method="get" action="<?= base_url('transaksi/akhir-tahun') ?>" id="formFilterAkhirTahun">
        <div class="row align-items-end">
          <!-- Tahun Ajaran -->
          <div class="col-md-5 mb-2 mb-md-0">
            <label for="tahun_ajaran_id" class="small font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran</label>
            <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control select2" onchange="this.form.submit()">
              <?php foreach ($tahunAjaran as $t) : ?>
                <option value="<?= $t['id'] ?>" <?= ($selectedTahunId == $t['id']) ? 'selected' : '' ?>>
                  <?= esc($t['nama_tahun_ajaran']) ?> <?= ($t['status'] == 'aktif') ? '(Aktif)' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Pilih Kelas (Concatenated Unique Display) -->
          <div class="col-md-5 mb-2 mb-md-0">
            <label for="kelas_id" class="small font-weight-bold"><i class="fas fa-school mr-1"></i> Pilih Kelas Siswa</label>
            <select name="kelas_id" id="kelas_id" class="form-control select2" onchange="this.form.submit()">
              <option value="">-- Pilih Kelas --</option>
              <?php foreach ($kelas as $k) : ?>
                <?php 
                  $taLabel = esc($selectedTahunInfo['nama_tahun_ajaran'] ?? 'Aktif');
                  $waliLabel = !empty($k['nama_wali_kelas']) ? $k['nama_wali_kelas'] : (!empty($k['nama_wali']) ? $k['nama_wali'] : 'Belum Ditentukan');
                ?>
                <option value="<?= $k['id'] ?>" <?= ($selectedKelasId == $k['id']) ? 'selected' : '' ?>>
                  <?= esc($k['nama_kelas']) ?> (TA <?= $taLabel ?> | Wali: <?= esc($waliLabel) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <button type="submit" class="btn btn-warning btn-block font-weight-bold shadow-sm">
              <i class="fas fa-search mr-1"></i> Tampilkan
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php if ($selectedKelasId && !empty($selectedKelas)) : ?>
    <!-- Summary Stat Cards -->
    <div class="row">
      <div class="col-md-4 col-sm-6">
        <div class="info-box bg-light border shadow-sm">
          <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-dark font-weight-bold">Total Siswa Kelas</span>
            <span class="info-box-number text-info font-weight-bold" style="font-size: 20px;"><?= $totalSiswa ?> Murid</span>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="info-box bg-light border shadow-sm">
          <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-dark font-weight-bold">Sisa Saldo Belum Ditarik</span>
            <span class="info-box-number text-danger font-weight-bold" style="font-size: 20px;">Rp <?= number_format($totalBelumDitarik, 0, ',', '.') ?></span>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-12">
        <div class="info-box bg-light border shadow-sm">
          <span class="info-box-icon <?= $isClassLunas ? 'bg-success' : 'bg-secondary' ?>"><i class="fas <?= $isClassLunas ? 'fa-check-circle' : 'fa-hourglass-half' ?>"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-dark font-weight-bold">Status Tabungan Kelas</span>
            <?php if ($isClassLunas) : ?>
              <span class="badge badge-success p-2 font-weight-bold" style="font-size: 13px;"><i class="fas fa-check-double mr-1"></i> SELURUH TABUNGAN LUNAS (Rp 0)</span>
            <?php else : ?>
              <span class="badge badge-warning p-2 font-weight-bold text-dark" style="font-size: 13px;"><i class="fas fa-sync-alt fa-spin mr-1"></i> Lunas <?= $countLunas ?> / <?= $totalSiswa ?> Siswa</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Student Table List -->
    <div class="card card-primary card-outline shadow-sm">
      <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
        <h3 class="card-title font-weight-bold text-dark mb-2 mb-md-0">
          <i class="fas fa-list-alt text-primary mr-2"></i>Daftar Penarikan Tabungan Siswa - <?= esc($selectedKelas['nama_kelas']) ?> (TA <?= esc($selectedTahunInfo['nama_tahun_ajaran'] ?? '') ?>)
        </h3>
        <?php if ($isClassLunas) : ?>
          <button type="button" class="btn btn-success font-weight-bold shadow-sm" id="btnLuluskanKelas">
            <i class="fas fa-user-graduate mr-1"></i> Set Status Seluruh Siswa Lulus (Kelas ini)
          </button>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover mb-0">
            <thead class="bg-light">
              <tr class="text-center">
                <th width="50">No</th>
                <th width="130">NIS</th>
                <th>Nama Lengkap Siswa</th>
                <th width="180" class="text-right">Saldo Tabungan (Rp)</th>
                <th width="160" class="text-center">Status Kelunasan</th>
                <th width="220" class="text-center">Aksi Bagi Tabungan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($siswaList as $idx => $s) : 
                $saldo = (float)$s['saldo_akhir'];
                $isLunas = ($saldo <= 0);
              ?>
                <tr>
                  <td class="text-center font-weight-bold"><?= $idx + 1 ?></td>
                  <td class="text-center"><span class="badge badge-light border"><?= esc($s['nis']) ?></span></td>
                  <td class="font-weight-bold text-dark"><?= esc($s['nama_lengkap']) ?></td>
                  <td class="text-right font-weight-bold <?= $isLunas ? 'text-muted' : 'text-success' ?>" style="font-size: 15px;">
                    Rp <?= number_format($saldo, 0, ',', '.') ?>
                  </td>
                  <td class="text-center">
                    <?php if ($isLunas) : ?>
                      <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sudah Lunas (Rp 0)</span>
                    <?php else : ?>
                      <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Ditarik</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if (!$isLunas) : ?>
                      <button type="button" class="btn btn-sm btn-warning font-weight-bold btn-tarik-lunas shadow-sm" data-id="<?= $s['siswa_id'] ?>" data-nama="<?= esc($s['nama_lengkap']) ?>" data-saldo="Rp <?= number_format($saldo, 0, ',', '.') ?>">
                        <i class="fas fa-hand-holding-usd mr-1"></i> Tarik Lunas (Bagi Tabungan)
                      </button>
                    <?php else : ?>
                      <span class="text-muted small font-weight-bold"><i class="fas fa-check-double text-success mr-1"></i> Selesai Dibagikan</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>

              <?php if (empty($siswaList)) : ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada data siswa terdaftar pada kelas dan tahun ajaran ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-info py-4 text-center shadow-sm">
      <i class="fas fa-arrow-up fa-2x mb-2 d-block text-info"></i>
      <h5>Silakan Pilih <strong>Tahun Ajaran</strong> & <strong>Kelas Siswa</strong> di atas untuk memulai penarikan tabungan akhir tahun!</h5>
    </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tombol Tarik Lunas Per Siswa
    document.querySelectorAll('.btn-tarik-lunas').forEach(btn => {
        btn.addEventListener('click', function() {
            const siswaId = this.dataset.id;
            const nama = this.dataset.nama;
            const saldoStr = this.dataset.saldo;
            const tahunId = document.getElementById('tahun_ajaran_id').value;

            Swal.fire({
                title: 'Konfirmasi Penarikan Akhir Tahun',
                html: `Apakah Anda yakin ingin menarik lunas sisa tabungan sebesar <strong>${saldoStr}</strong> milik siswa <strong>${nama}</strong>?<br><br><span class="text-muted small">Saldo akhir siswa akan menjadi <strong>Rp 0</strong>.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-hand-holding-usd mr-1"></i> Ya, Tarik Lunas Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses Penarikan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch('<?= base_url('transaksi/save-tarik-lunas') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        },
                        body: new URLSearchParams({
                            siswa_id: siswaId,
                            tahun_ajaran_id: tahunId,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Penarikan Lunas Berhasil!',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                        }
                    })
                    .catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error Network', text: err.message });
                    });
                }
            });
        });
    });

    // Tombol Set Status Lulus Seluruh Kelas
    const btnLuluskanKelas = document.getElementById('btnLuluskanKelas');
    if (btnLuluskanKelas) {
        btnLuluskanKelas.addEventListener('click', function() {
            const kelasId = '<?= $selectedKelasId ?>';
            const tahunId = '<?= $selectedTahunId ?>';

            Swal.fire({
                title: 'Konfirmasi Kelulusan Massal',
                html: `Seluruh tabungan siswa di kelas ini telah LUNAS (Rp 0).<br>Apakah Anda yakin ingin mengubah status seluruh siswa di kelas ini menjadi <strong>LULUS</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-user-graduate mr-1"></i> Ya, Set Status Lulus Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memperbarui Status Siswa...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch('<?= base_url('transaksi/set-lulus-kelas') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        },
                        body: new URLSearchParams({
                            kelas_id: kelasId,
                            tahun_ajaran_id: tahunId,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Selamat!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                        }
                    })
                    .catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error Network', text: err.message });
                    });
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
