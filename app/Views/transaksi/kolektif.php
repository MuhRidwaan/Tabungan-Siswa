<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-layer-group text-primary mr-2"></i><?= esc($title) ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('transaksi') ?>">Transaksi</a></li>
          <li class="breadcrumb-item active">Kolektif</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <!-- Banner Draft Alert -->
    <div id="draftAlert" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
      <i class="fas fa-save mr-2"></i>
      <strong id="draftAlertText">Draft setoran sementara terdeteksi dari sesi sebelumnya!</strong>
      <button type="button" class="btn btn-sm btn-outline-dark ml-3" id="btnResetDraft"><i class="fas fa-trash-alt"></i> Reset Form / Hapus Draft</button>
    </div>

    <!-- Main Card Header Controls -->
    <form id="formKolektif">
      <?= csrf_field() ?>
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter & Pengaturan Setoran Kolektif</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Pilih Kelas -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="kelas_id"><i class="fas fa-school mr-1"></i> Pilih Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" id="kelas_id" class="form-control select2" required>
                  <option value="">-- Pilih Kelas --</option>
                  <?php foreach ($kelas as $k) : ?>
                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?> (Wali: <?= esc($k['nama_wali'] ?? 'Belum Ditentukan') ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Jenis Transaksi -->
            <div class="col-md-3">
              <div class="form-group">
                <label for="jenis_transaksi"><i class="fas fa-exchange-alt mr-1"></i> Jenis Transaksi <span class="text-danger">*</span></label>
                <select name="jenis_transaksi" id="jenis_transaksi" class="form-control" required>
                  <option value="setor">🟢 Setor Tunai (Pemasukan)</option>
                  <option value="tarik">🔴 Tarik Tunai (Penarikan)</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="col-md-2">
              <div class="form-group">
                <label for="tanggal"><i class="fas fa-calendar-alt mr-1"></i> Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>

            <!-- Keterangan Umum -->
            <div class="col-md-3">
              <div class="form-group">
                <label for="keterangan_umum"><i class="fas fa-comment-alt mr-1"></i> Catatan/Keterangan</label>
                <input type="text" name="keterangan_umum" id="keterangan_umum" class="form-control" placeholder="Contoh: Setoran Hari Jumat">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table List Students Batch Entry -->
      <div class="card card-success card-outline">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h3 class="card-title"><i class="fas fa-users mr-1"></i> Daftar Input Nominal Siswa</h3>
          <div class="card-tools">
            <span id="draftStatusBadge" class="badge badge-secondary p-2 mr-2"><i class="fas fa-sync-alt fa-spin"></i> Memuat...</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnFillAllModal" data-toggle="modal" data-target="#modalFillAll">
              <i class="fas fa-calculator mr-1"></i> Isi Nominal Seragam
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover mb-0" id="tableSiswa">
              <thead class="bg-light">
                <tr>
                  <th width="50" class="text-center">No</th>
                  <th width="120">NIS</th>
                  <th>Nama Siswa</th>
                  <th width="180" class="text-right">Saldo Saat Ini</th>
                  <th width="220" class="text-center">Nominal (Rp)</th>
                  <th width="200">Keterangan Khusus</th>
                  <th width="180" class="text-right">Estimasi Saldo Baru</th>
                </tr>
              </thead>
              <tbody id="siswaContainer">
                <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                    <i class="fas fa-arrow-up text-primary fa-2x mb-2 d-block"></i>
                    Silakan pilih <strong>Kelas</strong> di atas untuk memuat daftar siswa.
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-light font-weight-bold d-none" id="tableFooter">
                <tr>
                  <td colspan="4" class="text-right">TOTAL REKAPITULASI KOLEKTIF:</td>
                  <td class="text-right text-success h5 mb-0" id="totalKolektifDisplay">Rp 0</td>
                  <td colspan="2"><span id="siswaTerisiDisplay" class="badge badge-info p-2">0 Siswa Terisi</span></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
          <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Input nominal 0 atau kosong akan otomatis dilewati saat penyimpanan.</span>
          <button type="button" class="btn btn-success btn-lg px-4" id="btnSimpanKolektif" disabled>
            <i class="fas fa-save mr-2"></i> Simpan Semua Transaksi Kolektif
          </button>
        </div>
      </div>
    </form>

  </div>
</section>

<!-- Modal Isi Nominal Seragam -->
<div class="modal fade" id="modalFillAll" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-coins mr-2"></i>Isi Nominal Seragam untuk Semua Siswa</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="nominalSeragam">Masukkan Nominal (Rp)</label>
          <input type="text" id="nominalSeragam" class="form-control form-control-lg" placeholder="Contoh: 10000">
        </div>
        <small class="text-muted">Nominal ini akan otomatis diisikan ke seluruh siswa di kelas yang tampil.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnApplyFillAll"><i class="fas fa-check mr-1"></i> Terapkan ke Semua</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('kelas_id');
    const jenisSelect = document.getElementById('jenis_transaksi');
    const tanggalInput = document.getElementById('tanggal');
    const ketUmumInput = document.getElementById('keterangan_umum');
    const siswaContainer = document.getElementById('siswaContainer');
    const tableFooter = document.getElementById('tableFooter');
    const btnSimpan = document.getElementById('btnSimpanKolektif');
    const draftStatusBadge = document.getElementById('draftStatusBadge');
    const draftAlert = document.getElementById('draftAlert');
    const btnResetDraft = document.getElementById('btnResetDraft');

    const STORAGE_KEY = 'tabungan_draft_kolektif';
    let currentSiswaList = [];

    // Helper format Rupiah
    function formatRupiah(val) {
        let num = String(val).replace(/[^0-9]/g, '');
        if (!num) return '';
        return parseInt(num, 10).toLocaleString('id-ID');
    }

    function parseRawNumber(val) {
        if (!val) return 0;
        return parseFloat(String(val).replace(/\./g, '').replace(/,/g, '')) || 0;
    }

    function getDraftKey() {
        const kId = kelasSelect.value || 'all';
        const tgl = tanggalInput.value || 'today';
        const jns = jenisSelect.value || 'setor';
        return `tabungan_draft_kolektif_${kId}_${tgl}_${jns}`;
    }

    // Auto-Save Draft to LocalStorage
    function saveDraftToStorage() {
        const nominals = {};
        const keterangans = {};

        document.querySelectorAll('.input-nominal').forEach(inp => {
            const sid = inp.dataset.siswaId;
            if (inp.value) nominals[sid] = inp.value;
        });

        document.querySelectorAll('.input-ket').forEach(inp => {
            const sid = inp.dataset.siswaId;
            if (inp.value) keterangans[sid] = inp.value;
        });

        const draftData = {
            kelasId: kelasSelect.value,
            jenis: jenisSelect.value,
            tanggal: tanggalInput.value,
            ketUmum: ketUmumInput.value,
            nominals: nominals,
            keterangans: keterangans,
            savedAt: new Date().toLocaleTimeString('id-ID')
        };

        localStorage.setItem(getDraftKey(), JSON.stringify(draftData));
        draftStatusBadge.className = 'badge badge-success p-2 mr-2';
        draftStatusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Draft tersimpan (' + draftData.savedAt + ')';
    }

    function loadDraftFromStorage() {
        const raw = localStorage.getItem(getDraftKey());
        if (!raw) {
            draftStatusBadge.className = 'badge badge-secondary p-2 mr-2';
            draftStatusBadge.innerHTML = '<i class="fas fa-info-circle"></i> Belum ada draft untuk tanggal ini';
            return null;
        }
        try {
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    // Load Students by Class AJAX
    function loadSiswaList(kelasId, callback) {
        const targetId = kelasId || 'all';
        siswaContainer.innerHTML = `<tr><td colspan="7" class="text-center text-primary py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Memuat daftar siswa...</td></tr>`;

        fetch(`<?= base_url('transaksi/get-siswa-by-kelas') ?>/${targetId}`)
            .then(res => res.json())
            .then(data => {
                currentSiswaList = data;
                renderSiswaRows(data);
                if (callback) callback();
            })
            .catch(err => {
                console.error(err);
                siswaContainer.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>Gagal memuat daftar siswa kelas.</td></tr>`;
            });
    }

    function renderSiswaRows(data) {
        if (!data || data.length === 0) {
            siswaContainer.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-user-slash fa-2x mb-2 d-block"></i>Belum ada siswa yang terdaftar di kelas ini pada Tahun Ajaran Aktif.</td></tr>`;
            tableFooter.classList.add('d-none');
            btnSimpan.disabled = true;
            return;
        }

        let html = '';

        data.forEach((s, idx) => {
            const saldo = parseFloat(s.saldo_akhir) || 0;
            html += `
                <tr>
                    <td class="text-center font-weight-bold">${idx + 1}</td>
                    <td><span class="badge badge-secondary">${s.nis}</span></td>
                    <td class="font-weight-bold">${s.nama_lengkap}</td>
                    <td class="text-right font-weight-bold text-primary">Rp ${saldo.toLocaleString('id-ID')}</td>
                    <td>
                        <input type="hidden" name="siswa_id[]" value="${s.siswa_id}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" name="nominal[]" data-siswa-id="${s.siswa_id}" data-saldo="${saldo}" class="form-control input-nominal text-right font-weight-bold" placeholder="0">
                        </div>
                    </td>
                    <td>
                        <input type="text" name="keterangan[]" data-siswa-id="${s.siswa_id}" class="form-control form-control-sm input-ket" placeholder="Opsional...">
                    </td>
                    <td class="text-right font-weight-bold estimasi-saldo" id="estimasi_${s.siswa_id}">
                        Rp ${saldo.toLocaleString('id-ID')}
                    </td>
                </tr>
            `;
        });

        siswaContainer.innerHTML = html;
        tableFooter.classList.remove('d-none');
        btnSimpan.disabled = false;

        attachInputEvents();
        recalculateTotals();
    }

    function attachInputEvents() {
        const nominalInputs = Array.from(document.querySelectorAll('.input-nominal'));
        nominalInputs.forEach((inp, idx) => {
            inp.addEventListener('input', function() {
                let formatted = formatRupiah(this.value);
                this.value = formatted;
                updateEstimasiRow(this);
                recalculateTotals();
                saveDraftToStorage();
            });

            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (idx + 1 < nominalInputs.length) {
                        nominalInputs[idx + 1].focus();
                        nominalInputs[idx + 1].select();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (idx - 1 >= 0) {
                        nominalInputs[idx - 1].focus();
                        nominalInputs[idx - 1].select();
                    }
                }
            });
        });

        document.querySelectorAll('.input-ket').forEach(inp => {
            inp.addEventListener('input', function() {
                saveDraftToStorage();
            });
        });
    }

    function updateEstimasiRow(inp) {
        const sid = inp.dataset.siswaId;
        const saldo = parseFloat(inp.dataset.saldo) || 0;
        const nominal = parseRawNumber(inp.value);
        const jenis = jenisSelect.value;
        const estElem = document.getElementById(`estimasi_${sid}`);

        let newSaldo = saldo;
        if (jenis === 'setor') {
            newSaldo = saldo + nominal;
            estElem.className = 'text-right font-weight-bold text-success';
        } else {
            newSaldo = saldo - nominal;
            if (newSaldo < 0) {
                estElem.className = 'text-right font-weight-bold text-danger';
            } else {
                estElem.className = 'text-right font-weight-bold text-warning';
            }
        }

        estElem.innerHTML = `Rp ${newSaldo.toLocaleString('id-ID')}`;
    }

    function recalculateTotals() {
        let grandTotal = 0;
        let countFilled = 0;

        document.querySelectorAll('.input-nominal').forEach(inp => {
            const val = parseRawNumber(inp.value);
            if (val > 0) {
                grandTotal += val;
                countFilled++;
            }
        });

        document.getElementById('totalKolektifDisplay').innerHTML = `Rp ${grandTotal.toLocaleString('id-ID')}`;
        document.getElementById('siswaTerisiDisplay').innerHTML = `${countFilled} Siswa Terisi`;
    }

    // Event listeners
    kelasSelect.addEventListener('change', function() {
        loadSiswaList(this.value, function() {
            saveDraftToStorage();
        });
    });

    jenisSelect.addEventListener('change', function() {
        document.querySelectorAll('.input-nominal').forEach(inp => updateEstimasiRow(inp));
        recalculateTotals();
        saveDraftToStorage();
    });

    function checkAndRestoreDraft() {
        const draft = loadDraftFromStorage();
        if (draft && draft.nominals) {
            Object.keys(draft.nominals).forEach(sid => {
                const inp = document.querySelector(`.input-nominal[data-siswa-id="${sid}"]`);
                if (inp) {
                    inp.value = draft.nominals[sid];
                    updateEstimasiRow(inp);
                }
            });
            if (draft.keterangans) {
                Object.keys(draft.keterangans).forEach(sid => {
                    const inp = document.querySelector(`.input-ket[data-siswa-id="${sid}"]`);
                    if (inp) inp.value = draft.keterangans[sid];
                });
            }
            recalculateTotals();
        } else {
            document.querySelectorAll('.input-nominal').forEach(inp => { inp.value = ''; updateEstimasiRow(inp); });
            document.querySelectorAll('.input-ket').forEach(inp => { inp.value = ''; });
            recalculateTotals();
        }
    }

    tanggalInput.addEventListener('change', function() {
        checkAndRestoreDraft();
        saveDraftToStorage();
    });

    ketUmumInput.addEventListener('input', saveDraftToStorage);

    // Apply Fill All
    document.getElementById('btnApplyFillAll').addEventListener('click', function() {
        const val = document.getElementById('nominalSeragam').value;
        const formatted = formatRupiah(val);
        document.querySelectorAll('.input-nominal').forEach(inp => {
            inp.value = formatted;
            updateEstimasiRow(inp);
        });
        recalculateTotals();
        saveDraftToStorage();
        $('#modalFillAll').modal('hide');
    });

    // Reset Draft
    btnResetDraft.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus draft setoran untuk tanggal ini?')) {
            localStorage.removeItem(getDraftKey());
            draftAlert.classList.add('d-none');
            location.reload();
        }
    });

    // Check existing draft on load
    const existingDraft = loadDraftFromStorage();
    if (existingDraft && existingDraft.kelasId) {
        draftAlert.classList.remove('d-none');
        document.getElementById('draftAlertText').innerHTML = `<i class="fas fa-history mr-1"></i> Draft setoran tersimpan otomatis (${existingDraft.savedAt}) ditemukan!`;
        
        kelasSelect.value = existingDraft.kelasId;
        if (existingDraft.jenis) jenisSelect.value = existingDraft.jenis;
        if (existingDraft.tanggal) tanggalInput.value = existingDraft.tanggal;
        if (existingDraft.ketUmum) ketUmumInput.value = existingDraft.ketUmum;

        loadSiswaList(existingDraft.kelasId, function() {
            if (existingDraft.nominals) {
                Object.keys(existingDraft.nominals).forEach(sid => {
                    const inp = document.querySelector(`.input-nominal[data-siswa-id="${sid}"]`);
                    if (inp) {
                        inp.value = existingDraft.nominals[sid];
                        updateEstimasiRow(inp);
                    }
                });
            }
            if (existingDraft.keterangans) {
                Object.keys(existingDraft.keterangans).forEach(sid => {
                    const inp = document.querySelector(`.input-ket[data-siswa-id="${sid}"]`);
                    if (inp) inp.value = existingDraft.keterangans[sid];
                });
            }
            recalculateTotals();
        });
    } else {
        loadSiswaList('all');
    }

    // Submit Collective Batch
    btnSimpan.addEventListener('click', function() {
        let countFilled = 0;
        document.querySelectorAll('.input-nominal').forEach(inp => {
            if (parseRawNumber(inp.value) > 0) countFilled++;
        });

        if (countFilled === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Nominal Kosong!',
                text: 'Silakan isi setidaknya 1 nominal setoran/penarikan siswa untuk disimpan.'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Penyimpanan',
            text: `Apakah Anda yakin ingin menyimpan ${countFilled} transaksi kolektif ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Simpan Transaksi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Penyimpanan...';

                const formData = new FormData(document.getElementById('formKolektif'));

                fetch('<?= base_url('transaksi/save-kolektif') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status) {
                        localStorage.removeItem(getDraftKey());
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Kolektif Berhasil!',
                            text: data.message || 'Seluruh transaksi kolektif berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('transaksi') ?>';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan!',
                            text: data.message || 'Terjadi kesalahan saat menyimpan.'
                        });
                        btnSimpan.disabled = false;
                        btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Semua Transaksi Kolektif';
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Terputus!',
                        text: 'Terjadi kesalahan jaringan/server saat menyimpan.'
                    });
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Semua Transaksi Kolektif';
                });
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
