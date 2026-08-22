<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-calendar-alt text-info mr-2"></i><?= esc($title) ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('transaksi') ?>">Transaksi</a></li>
          <li class="breadcrumb-item active">Multi-Tanggal</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <!-- Filter Header Card -->
    <div class="card card-info card-outline">
      <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
          <h3 class="card-title font-weight-bold text-dark mb-2 mb-md-0">
            <i class="fas fa-user-clock text-info mr-2"></i>Pilih Siswa & Periode Tanggal Harian
          </h3>
          <div class="ml-auto text-right">
            <button type="button" class="btn btn-sm btn-success mr-2 mb-1" data-toggle="modal" data-target="#modalImportMulti">
              <i class="fas fa-file-excel mr-1"></i> Import Excel Transaksi
            </button>
            <a href="<?= base_url('transaksi/download-template-multi') ?>" class="btn btn-sm btn-outline-success mb-1">
              <i class="fas fa-file-download mr-1"></i> Download Template Excel (.xls)
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <form id="formFilterMulti">
          <div class="row align-items-end">
            <!-- Pilih Siswa (3 Columns) -->
            <div class="col-md-3">
              <div class="form-group mb-md-0">
                <label for="siswa_id"><i class="fas fa-user mr-1"></i> Nama Siswa <span class="text-danger">*</span></label>
                <select name="siswa_id" id="siswa_id" class="form-control select2" required style="width:100%;">
                  <option value="">-- Pilih Siswa --</option>
                  <?php foreach ($siswa as $s) : ?>
                    <option value="<?= $s['id'] ?>" data-saldo="<?= $s['saldo_akhir'] ?>">
                      <?= esc($s['nama_lengkap']) ?> (NIS: <?= esc($s['nis']) ?> | Saldo: Rp <?= number_format($s['saldo_akhir'], 0, ',', '.') ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Jenis Transaksi (3 Columns) -->
            <div class="col-md-3">
              <div class="form-group mb-md-0">
                <label for="jenis_transaksi"><i class="fas fa-exchange-alt mr-1"></i> Jenis Transaksi <span class="text-danger">*</span></label>
                <select name="jenis_transaksi" id="jenis_transaksi" class="form-control select2" required style="width:100%;">
                  <option value="setor">🟢 Setor Tunai (Pemasukan)</option>
                  <option value="tarik">🔴 Tarik Tunai (Penarikan)</option>
                </select>
              </div>
            </div>

            <!-- Tanggal Mulai (2 Columns) -->
            <div class="col-md-2 col-6">
              <div class="form-group mb-md-0">
                <label for="tgl_mulai"><i class="fas fa-calendar-day mr-1"></i> Dari Tanggal</label>
                <input type="date" id="tgl_mulai" class="form-control" value="<?= date('Y-m-01') ?>" required>
              </div>
            </div>

            <!-- Tanggal Selesai (2 Columns) -->
            <div class="col-md-2 col-6">
              <div class="form-group mb-md-0">
                <label for="tgl_selesai"><i class="fas fa-calendar-check mr-1"></i> Sampai Tanggal</label>
                <input type="date" id="tgl_selesai" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>

            <!-- Generate Button (2 Columns) -->
            <div class="col-md-2 mt-3 mt-md-0">
              <button type="button" class="btn btn-info btn-block shadow-sm" id="btnGenerateGrid">
                <i class="fas fa-sync-alt mr-1"></i> Buat Grid
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Table Matrix Multi-Tanggal -->
    <div class="card card-primary card-outline">
      <div class="card-header d-flex flex-wrap align-items-center justify-content-between py-3">
        <h3 class="card-title font-weight-bold mb-2 mb-md-0" id="tableTitle">
          <i class="fas fa-list-ol mr-2"></i>Tabel Setoran Harian Siswa
        </h3>
        <div class="card-tools d-flex align-items-center flex-wrap ml-auto">
          <span id="draftStatusBadge" class="badge badge-secondary p-2 mr-2 mb-1">
            <i class="fas fa-info-circle mr-1"></i> Pilih siswa & periode tanggal di atas
          </span>
          <button type="button" class="btn btn-sm btn-outline-primary mb-1" id="btnBulkNominal">
            <i class="fas fa-calculator mr-1"></i> Isi Nominal Seragam
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <form id="formSaveMulti">
          <?= csrf_field() ?>
          <input type="hidden" name="siswa_id" id="post_siswa_id">
          <input type="hidden" name="jenis_transaksi" id="post_jenis_transaksi">

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover mb-0">
              <thead class="bg-light">
                <tr>
                  <th width="50" class="text-center">No</th>
                  <th width="200">Tanggal</th>
                  <th width="260" class="text-center">Nominal Setoran / Tarik (Rp)</th>
                  <th>Keterangan Khusus (Opsional)</th>
                  <th width="220" class="text-right">Estimasi Saldo Akumulatif</th>
                </tr>
              </thead>
              <tbody id="gridContainer">
                <tr>
                  <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-arrow-up text-info fa-2x mb-2 d-block"></i>
                    Silakan pilih <strong>Nama Siswa</strong> dan <strong>Rentang Tanggal</strong> di atas, lalu klik <strong>Buat Grid</strong>.
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-light font-weight-bold d-none" id="tableFooter">
                <tr>
                  <td colspan="2" class="text-right">TOTAL AKUMULASI TRANSAKSI:</td>
                  <td class="text-right text-success h5 mb-0" id="totalNominalDisplay">Rp 0</td>
                  <td colspan="2"><span id="hariTerisiDisplay" class="badge badge-info p-2">0 Hari Terisi</span></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </form>
      </div>
      <div class="card-footer bg-light py-3">
        <div class="row w-100 align-items-center m-0">
          <div class="col-md-7 p-0 mb-2 mb-md-0">
            <span class="text-muted small"><i class="fas fa-info-circle mr-1"></i> Tekan <strong>Enter</strong> atau <strong>Panah Bawah (↓)</strong> untuk berpindah tanggal dengan cepat.</span>
          </div>
          <div class="col-md-5 p-0 text-md-right text-left">
            <button type="button" class="btn btn-success btn-lg px-4 shadow-sm" id="btnSimpanMulti" disabled>
              <i class="fas fa-save mr-2"></i> Simpan Setoran Multi-Tanggal
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const siswaSelect = document.getElementById('siswa_id');
    const jenisSelect = document.getElementById('jenis_transaksi');
    const tglMulaiInput = document.getElementById('tgl_mulai');
    const tglSelesaiInput = document.getElementById('tgl_selesai');
    const btnGenerate = document.getElementById('btnGenerateGrid');
    const gridContainer = document.getElementById('gridContainer');
    const tableFooter = document.getElementById('tableFooter');
    const btnSimpan = document.getElementById('btnSimpanMulti');
    const draftStatusBadge = document.getElementById('draftStatusBadge');
    const tableTitle = document.getElementById('tableTitle');

    const STORAGE_KEY = 'tabungan_draft_multi_tanggal';

    function formatRupiah(val) {
        let num = String(val).replace(/[^0-9]/g, '');
        if (!num) return '';
        return parseInt(num, 10).toLocaleString('id-ID');
    }

    function parseRawNumber(val) {
        if (!val) return 0;
        return parseFloat(String(val).replace(/\./g, '').replace(/,/g, '')) || 0;
    }

    function formatDateIndo(dateStr) {
        let [y, m, d] = dateStr.split('-').map(Number);
        const dt = new Date(y, m - 1, d);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return dt.toLocaleDateString('id-ID', options);
    }

    function generateDateArray(startDateStr, endDateStr) {
        const dates = [];
        let [y1, m1, d1] = startDateStr.split('-').map(Number);
        let [y2, m2, d2] = endDateStr.split('-').map(Number);

        let curr = new Date(y1, m1 - 1, d1);
        let end  = new Date(y2, m2 - 1, d2);

        while (curr <= end) {
            let year  = curr.getFullYear();
            let month = String(curr.getMonth() + 1).padStart(2, '0');
            let day   = String(curr.getDate()).padStart(2, '0');
            dates.push(`${year}-${month}-${day}`);
            curr.setDate(curr.getDate() + 1);
        }
        return dates;
    }

    function renderGrid(dates, initialValues = {}) {
        const siswaId = siswaSelect.value;
        const selectedOpt = siswaSelect.options[siswaSelect.selectedIndex];
        const initialSaldo = parseFloat(selectedOpt ? selectedOpt.dataset.saldo : 0) || 0;
        const jenis = jenisSelect.value;

        if (!siswaId || dates.length === 0) {
            gridContainer.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-arrow-up text-info fa-2x mb-2 d-block"></i>Silakan pilih <strong>Nama Siswa</strong> dan rentang tanggal di atas.</td></tr>`;
            tableFooter.classList.add('d-none');
            btnSimpan.disabled = true;
            return;
        }

        let html = '';
        dates.forEach((dStr, idx) => {
            const formattedDate = formatDateIndo(dStr);
            const valNominal = initialValues[dStr] ? initialValues[dStr].nominal || '' : '';
            const valKet = initialValues[dStr] ? initialValues[dStr].ket || '' : '';

            html += `
                <tr>
                    <td class="text-center font-weight-bold">${idx + 1}</td>
                    <td>
                        <input type="hidden" name="tanggal[]" value="${dStr}">
                        <strong class="d-block">${formattedDate}</strong>
                        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>${dStr}</small>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" name="nominal[]" data-date="${dStr}" class="form-control input-nominal text-right font-weight-bold" placeholder="0" value="${valNominal}">
                        </div>
                    </td>
                    <td>
                        <input type="text" name="keterangan[]" data-date="${dStr}" class="form-control form-control-sm input-ket" placeholder="Opsional..." value="${valKet}">
                    </td>
                    <td class="text-right font-weight-bold estimasi-saldo" id="estimasi_${dStr}">
                        Rp ${initialSaldo.toLocaleString('id-ID')}
                    </td>
                </tr>
            `;
        });

        gridContainer.innerHTML = html;
        tableFooter.classList.remove('d-none');
        btnSimpan.disabled = false;

        document.getElementById('post_siswa_id').value = siswaId;
        document.getElementById('post_jenis_transaksi').value = jenis;

        attachInputEvents(initialSaldo);
        recalculateTotals(initialSaldo);
    }

    function attachInputEvents(initialSaldo) {
        const nominalInputs = Array.from(document.querySelectorAll('.input-nominal'));

        nominalInputs.forEach((inp, idx) => {
            inp.addEventListener('input', function() {
                this.value = formatRupiah(this.value);
                recalculateTotals(initialSaldo);
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
            inp.addEventListener('input', saveDraftToStorage);
        });
    }

    function recalculateTotals(initialSaldo) {
        const jenis = jenisSelect.value;
        let runningSaldo = initialSaldo;
        let grandTotal = 0;
        let countFilled = 0;

        document.querySelectorAll('.input-nominal').forEach(inp => {
            const dStr = inp.dataset.date;
            const nominal = parseRawNumber(inp.value);
            const estElem = document.getElementById(`estimasi_${dStr}`);

            if (nominal > 0) {
                grandTotal += nominal;
                countFilled++;
            }

            if (jenis === 'setor') {
                runningSaldo += nominal;
                if (estElem) estElem.className = 'text-right font-weight-bold text-success';
            } else {
                runningSaldo -= nominal;
                if (estElem) estElem.className = (runningSaldo < 0) ? 'text-right font-weight-bold text-danger' : 'text-right font-weight-bold text-warning';
            }

            if (estElem) estElem.innerHTML = `Rp ${runningSaldo.toLocaleString('id-ID')}`;
        });

        document.getElementById('totalNominalDisplay').innerHTML = `Rp ${grandTotal.toLocaleString('id-ID')}`;
        document.getElementById('hariTerisiDisplay').innerHTML = `${countFilled} Hari Terisi`;
    }

    function saveDraftToStorage() {
        const siswaId = siswaSelect.value;
        if (!siswaId) return;

        const dateValues = {};
        document.querySelectorAll('.input-nominal').forEach(inp => {
            const dStr = inp.dataset.date;
            const ketInp = document.querySelector(`.input-ket[data-date="${dStr}"]`);
            if (inp.value || (ketInp && ketInp.value)) {
                dateValues[dStr] = {
                    nominal: inp.value,
                    ket: ketInp ? ketInp.value : ''
                };
            }
        });

        const draftData = {
            siswaId: siswaId,
            jenis: jenisSelect.value,
            tglMulai: tglMulaiInput.value,
            tglSelesai: tglSelesaiInput.value,
            dateValues: dateValues,
            savedAt: new Date().toLocaleTimeString('id-ID')
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(draftData));
        draftStatusBadge.className = 'badge badge-success p-2';
        draftStatusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Draft tersimpan (' + draftData.savedAt + ')';
    }

    // Fitur Isi Nominal Seragam
    const btnBulkNominal = document.getElementById('btnBulkNominal');
    if (btnBulkNominal) {
        btnBulkNominal.addEventListener('click', function() {
            const nominalInputs = document.querySelectorAll('.input-nominal');
            if (nominalInputs.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Grid Belum Terbuat',
                        text: 'Silakan pilih siswa dan periode tanggal lalu klik "Buat Grid" terlebih dahulu!'
                    });
                } else {
                    alert('Silakan pilih siswa dan periode tanggal lalu klik "Buat Grid" terlebih dahulu!');
                }
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Isi Nominal Seragam',
                    text: 'Masukkan nominal yang ingin diterapkan ke seluruh baris tanggal:',
                    input: 'text',
                    inputPlaceholder: 'Contoh: 10.000',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Terapkan ke Semua Tanggal',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#007bff',
                    inputValidator: (value) => {
                        if (!value || parseRawNumber(value) <= 0) {
                            return 'Masukkan nominal angka yang valid!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formattedVal = formatRupiah(result.value);
                        nominalInputs.forEach(inp => {
                            inp.value = formattedVal;
                        });
                        const selectedOpt = siswaSelect.options[siswaSelect.selectedIndex];
                        const initialSaldo = parseFloat(selectedOpt ? selectedOpt.dataset.saldo : 0) || 0;
                        recalculateTotals(initialSaldo);
                        saveDraftToStorage();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Diterapkan!',
                            text: `Nominal Rp ${formattedVal} telah diterapkan ke ${nominalInputs.length} tanggal.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                const inputVal = prompt('Masukkan nominal seragam untuk seluruh tanggal:');
                if (inputVal && parseRawNumber(inputVal) > 0) {
                    const formattedVal = formatRupiah(inputVal);
                    nominalInputs.forEach(inp => {
                        inp.value = formattedVal;
                    });
                    const selectedOpt = siswaSelect.options[siswaSelect.selectedIndex];
                    const initialSaldo = parseFloat(selectedOpt ? selectedOpt.dataset.saldo : 0) || 0;
                    recalculateTotals(initialSaldo);
                    saveDraftToStorage();
                }
            }
        });
    }

    // Support Select2 jQuery events
    if (typeof $ !== 'undefined') {
        $('#siswa_id').on('change select2:select', function() {
            if (tglMulaiInput.value && tglSelesaiInput.value && this.value) {
                const dates = generateDateArray(tglMulaiInput.value, tglSelesaiInput.value);
                renderGrid(dates);
            }
        });

        $('#jenis_transaksi').on('change select2:select', function() {
            const selectedOpt = siswaSelect.options[siswaSelect.selectedIndex];
            const initialSaldo = parseFloat(selectedOpt ? selectedOpt.dataset.saldo : 0) || 0;
            recalculateTotals(initialSaldo);
            saveDraftToStorage();
        });
    }

    btnGenerate.addEventListener('click', function() {
        const siswaId = siswaSelect.value;
        const startDate = tglMulaiInput.value;
        const endDate = tglSelesaiInput.value;

        if (!siswaId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Pilih Siswa', text: 'Silakan pilih nama siswa terlebih dahulu!' });
            } else {
                alert('Silakan pilih nama siswa terlebih dahulu!');
            }
            return;
        }
        if (!startDate || !endDate) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Pilih Tanggal', text: 'Silakan tentukan tanggal mulai dan tanggal selesai!' });
            } else {
                alert('Silakan tentukan tanggal mulai dan tanggal selesai!');
            }
            return;
        }
        if (new Date(startDate) > new Date(endDate)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Rentang Tanggal Salah', text: 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai!' });
            } else {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!');
            }
            return;
        }

        const dates = generateDateArray(startDate, endDate);
        renderGrid(dates);
        saveDraftToStorage();
    });

    btnSimpan.addEventListener('click', function() {
        const siswaId = siswaSelect.value;
        if (!siswaId) {
            Swal.fire({ icon: 'warning', title: 'Pilih Siswa', text: 'Pilih siswa terlebih dahulu!' });
            return;
        }

        let countFilled = 0;
        document.querySelectorAll('.input-nominal').forEach(inp => {
            if (parseRawNumber(inp.value) > 0) countFilled++;
        });

        if (countFilled === 0) {
            Swal.fire({ icon: 'warning', title: 'Nominal Kosong', text: 'Silakan isi nominal setoran pada setidaknya 1 tanggal!' });
            return;
        }

        const selectedOpt = siswaSelect.options[siswaSelect.selectedIndex];
        const namaSiswa = selectedOpt ? selectedOpt.text : 'Siswa';

        Swal.fire({
            title: 'Konfirmasi Penyimpanan',
            html: `Apakah Anda yakin ingin menyimpan <strong>${countFilled} transaksi harian</strong> untuk <strong>${namaSiswa}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Simpan Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Penyimpanan...';

                const formData = new FormData(document.getElementById('formSaveMulti'));

                fetch('<?= base_url('transaksi/save-multi-tanggal') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        localStorage.removeItem(STORAGE_KEY);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('transaksi') ?>';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: data.message });
                        btnSimpan.disabled = false;
                        btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Setoran Multi-Tanggal';
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Server Error', text: 'Terjadi kesalahan server saat menyimpan transaksi multi-tanggal.' });
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Setoran Multi-Tanggal';
                });
            }
        });
    });
});
</script>

<!-- Modal Import Transaksi Multi-Tanggal -->
<div class="modal fade" id="modalImportMulti" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i>Import Transaksi Multi-Tanggal dari Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('transaksi/import-multi') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file_excel">Pilih File Excel (.xls / .xlsx / .csv)</label>
                        <input type="file" name="file_excel" id="file_excel" class="form-control-file" accept=".xls,.xlsx,.csv" required>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Format file harus sesuai template Excel (.xls) atau CSV (kolom: <code>nis, nama_lengkap, tanggal, jenis_transaksi, nominal, keterangan</code>).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload mr-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
