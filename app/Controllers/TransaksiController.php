<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Transaksi; // Sesuaikan nama model
use App\Models\Siswa;     // Sesuaikan nama model

class TransaksiController extends BaseController
{
    protected $transaksiModel;
    protected $siswaModel;
    protected $db; // Tambahkan properti untuk database

    public function __construct()
    {
        $this->transaksiModel = new Transaksi();
        $this->siswaModel = new Siswa();
        $this->db = \Config\Database::connect(); // Inisialisasi koneksi database
    }

    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?: 10;
        $search = $this->request->getGet('q');

        $data = [
            'title'       => 'Semua Transaksi',
            'transaksi'   => $this->transaksiModel->getTransaksiWithDetails($search, $perPage),
            'pager'       => $this->transaksiModel->pager,
            'perPage'     => $perPage,
            'search'      => $search,
            'siswa'       => $this->siswaModel->where('status_siswa', 'aktif')->findAll() // Untuk form modal
        ];
        return view('transaksi/index', $data);
    }

    /**
     * Endpoint AJAX untuk menyimpan (Create/Update) transaksi
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'siswa_id'        => 'required',
            'jenis_transaksi' => 'required',
            'jumlah'          => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }
        
        $id = $this->request->getPost('id');
        $siswa_id = $this->request->getPost('siswa_id');
        $jenis = $this->request->getPost('jenis_transaksi');
        $jumlah = (float) str_replace(['.', ','], '', $this->request->getPost('jumlah')); // Hapus format ribuan
        
        $siswa = $this->siswaModel->find($siswa_id);
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Siswa tidak ditemukan.']);
        }

        $this->db->transStart();
        
        $saldo_sebelum = (float) $siswa['saldo_akhir'];
        $saldo_sesudah = 0;

        if ($jenis == 'setor') {
            $saldo_sesudah = $saldo_sebelum + $jumlah;
        } else { // Tarik
            if ($jumlah > $saldo_sebelum) {
                $this->db->transRollback(); // Batalkan transaksi jika saldo tidak cukup
                return $this->response->setJSON(['success' => false, 'message' => 'Saldo tidak mencukupi untuk penarikan.']);
            }
            $saldo_sesudah = $saldo_sebelum - $jumlah;
        }

        $dataTransaksi = [
            'siswa_id'        => $siswa_id,
            'jenis_transaksi' => $jenis,
            'jumlah'          => $jumlah,
            'keterangan'      => $this->request->getPost('keterangan'),
            'saldo_sebelum'   => $saldo_sebelum,
            'saldo_sesudah'   => $saldo_sesudah,
            'pengguna_id'     => session()->get('id'), // Ambil dari session login (pastikan key session benar)
        ];

        if ($id) { // Update
            // Logika update transaksi bisa jadi kompleks, untuk saat ini kita nonaktifkan
            // $this->transaksiModel->update($id, $dataTransaksi);
            $message = 'Fitur update belum diaktifkan.';
            // return $this->response->setJSON(['success' => false, 'message' => $message]);
        } else { // Create
            $dataTransaksi['kode_transaksi'] = $this->transaksiModel->generateKodeTransaksi();
            $this->transaksiModel->insert($dataTransaksi);
            $message = 'Transaksi berhasil ditambahkan.';
        }
        
        // Update saldo akhir siswa
        $this->siswaModel->update($siswa_id, ['saldo_akhir' => $saldo_sesudah]);
        
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan transaksi karena error database.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    /**
     * Menampilkan halaman pencatatan transaksi kolektif
     */
    public function kolektif()
    {
        $kelasModel = new \App\Models\Kelas();
        $tahunAjaranModel = new \App\Models\TahunAjaran();

        $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();

        $data = [
            'title'       => 'Pencatatan Transaksi Kolektif',
            'kelas'       => $kelasModel->getAllKelasWithWali(),
            'tahunAktif'  => $tahunAktif
        ];
        return view('transaksi/kolektif', $data);
    }

    /**
     * AJAX Endpoint untuk mengambil daftar siswa per kelas pada tahun ajaran aktif
     */
    public function getSiswaByKelas($kelasId = null)
    {
        $siswaList = [];
        if ($kelasId && $kelasId !== 'all') {
            $tahunAjaranModel = new \App\Models\TahunAjaran();
            $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();
            if ($tahunAktif) {
                $riwayatModel = new \App\Models\RiwayatKelasSiswa();
                $siswaList = $riwayatModel->getSiswaByKelasTahun($kelasId, $tahunAktif['id']);
            }
        }

        // Fallback: Jika belum ada riwayat kelas, ambil seluruh siswa aktif dari master
        if (empty($siswaList)) {
            $rawSiswa = $this->siswaModel->where('status_siswa', 'aktif')->orderBy('nama_lengkap', 'ASC')->findAll();
            foreach ($rawSiswa as $s) {
                $siswaList[] = [
                    'siswa_id'    => $s['id'],
                    'nis'         => $s['nis'],
                    'nama_lengkap'=> $s['nama_lengkap'],
                    'saldo_akhir' => $s['saldo_akhir']
                ];
            }
        }

        return $this->response->setJSON($siswaList);
    }

    /**
     * AJAX Endpoint untuk menyimpan transaksi kolektif sekaligus
     */
    public function saveKolektif()
    {
        $jenis = $this->request->getPost('jenis_transaksi') ?: 'setor';
        $tanggalInput = $this->request->getPost('tanggal') ?: date('Y-m-d');
        $keteranganUmum = $this->request->getPost('keterangan_umum') ?: '';

        $siswaIds = $this->request->getPost('siswa_id');
        $nominals = $this->request->getPost('nominal');
        $keteranganList = $this->request->getPost('keterangan');

        if (empty($siswaIds) || !is_array($siswaIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data siswa yang dikirim.']);
        }

        $this->db->transStart();
        $countSuccess = 0;

        foreach ($siswaIds as $index => $siswaId) {
            $rawNominal = isset($nominals[$index]) ? $nominals[$index] : 0;
            $jumlah = (float) str_replace(['.', ','], '', (string)$rawNominal);

            if ($jumlah <= 0) {
                continue;
            }

            $siswa = $this->siswaModel->find($siswaId);
            if (!$siswa) {
                continue;
            }

            $saldo_sebelum = (float) $siswa['saldo_akhir'];
            $saldo_sesudah = 0;

            if ($jenis == 'setor') {
                $saldo_sesudah = $saldo_sebelum + $jumlah;
            } else {
                if ($jumlah > $saldo_sebelum) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Saldo siswa " . $siswa['nama_lengkap'] . " tidak mencukupi untuk penarikan Rp " . number_format($jumlah, 0, ',', '.')
                    ]);
                }
                $saldo_sesudah = $saldo_sebelum - $jumlah;
            }

            $ket = !empty($keteranganList[$index]) ? $keteranganList[$index] : $keteranganUmum;

            $dataTransaksi = [
                'kode_transaksi'  => $this->transaksiModel->generateKodeTransaksi(),
                'siswa_id'        => $siswaId,
                'jenis_transaksi' => $jenis,
                'jumlah'          => $jumlah,
                'keterangan'      => $ket,
                'saldo_sebelum'   => $saldo_sebelum,
                'saldo_sesudah'   => $saldo_sesudah,
                'pengguna_id'     => session()->get('id') ?? 1,
                'created_at'      => $tanggalInput . ' ' . date('H:i:s'),
            ];

            $this->transaksiModel->insert($dataTransaksi);
            $this->siswaModel->update($siswaId, ['saldo_akhir' => $saldo_sesudah]);
            $countSuccess++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan transaksi kolektif karena error database.']);
        }

        if ($countSuccess == 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada transaksi dengan nominal > 0 yang diisi.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Berhasil menyimpan {$countSuccess} transaksi kolektif!"
        ]);
    }

    /**
     * Menampilkan halaman pencatatan transaksi multi-tanggal per-siswa
     */
    public function multiTanggal()
    {
        $data = [
            'title' => 'Pencatatan Setoran Multi-Tanggal (Per-Siswa)',
            'siswa' => $this->siswaModel->where('status_siswa', 'aktif')->orderBy('nama_lengkap', 'ASC')->findAll()
        ];
        return view('transaksi/multi_tanggal', $data);
    }

    /**
     * AJAX Endpoint untuk menyimpan transaksi multi-tanggal harian untuk 1 siswa
     */
    public function saveMultiTanggal()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $jenis = $this->request->getPost('jenis_transaksi') ?: 'setor';

        $tanggals = $this->request->getPost('tanggal');
        $nominals = $this->request->getPost('nominal');
        $keteranganList = $this->request->getPost('keterangan');

        if (!$siswaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan pilih siswa terlebih dahulu.']);
        }

        if (empty($tanggals) || !is_array($tanggals)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada tanggal transaksi yang dikirim.']);
        }

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }

        $this->db->transStart();
        $countSuccess = 0;
        $runningSaldo = (float) $siswa['saldo_akhir'];

        foreach ($tanggals as $index => $tgl) {
            $rawNominal = isset($nominals[$index]) ? $nominals[$index] : 0;
            $jumlah = (float) str_replace(['.', ','], '', (string)$rawNominal);

            if ($jumlah <= 0) {
                continue;
            }

            $saldo_sebelum = $runningSaldo;
            $saldo_sesudah = 0;

            if ($jenis == 'setor') {
                $saldo_sesudah = $saldo_sebelum + $jumlah;
            } else {
                if ($jumlah > $saldo_sebelum) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Saldo siswa pada tanggal {$tgl} tidak mencukupi untuk penarikan Rp " . number_format($jumlah, 0, ',', '.')
                    ]);
                }
                $saldo_sesudah = $saldo_sebelum - $jumlah;
            }

            $ket = !empty($keteranganList[$index]) ? $keteranganList[$index] : "Setoran Harian Tgl {$tgl}";

            $dataTransaksi = [
                'kode_transaksi'  => $this->transaksiModel->generateKodeTransaksi(),
                'siswa_id'        => $siswaId,
                'jenis_transaksi' => $jenis,
                'jumlah'          => $jumlah,
                'keterangan'      => $ket,
                'saldo_sebelum'   => $saldo_sebelum,
                'saldo_sesudah'   => $saldo_sesudah,
                'pengguna_id'     => session()->get('id') ?? 1,
                'created_at'      => $tgl . ' ' . date('H:i:s'),
            ];

            $this->transaksiModel->insert($dataTransaksi);
            $runningSaldo = $saldo_sesudah;
            $countSuccess++;
        }

        if ($countSuccess > 0) {
            $this->siswaModel->update($siswaId, ['saldo_akhir' => $runningSaldo]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan transaksi multi-tanggal karena error database.']);
        }

        if ($countSuccess == 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada nominal > 0 yang diisi pada tabel tanggal.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Berhasil menyimpan {$countSuccess} transaksi harian untuk " . $siswa['nama_lengkap'] . "!"
        ]);
    }

    /**
     * Download Template CSV untuk Import Transaksi Multi-Tanggal
     */
    public function downloadTemplateMulti()
    {
        $filename = "template_import_transaksi_multi.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['nis', 'nama_lengkap', 'tanggal', 'jenis_transaksi', 'nominal', 'keterangan']);
        fputcsv($output, ['1001', 'Ahmad Dani', date('Y-m-01'), 'setor', '10000', 'Setoran Tanggal 1']);
        fputcsv($output, ['1001', 'Ahmad Dani', date('Y-m-02'), 'setor', '15000', 'Setoran Tanggal 2']);
        fputcsv($output, ['1002', 'Siti Rahma', date('Y-m-01'), 'setor', '20000', 'Setoran Tanggal 1']);
        fclose($output);
        exit;
    }

    /**
     * Import Rekap Transaksi Multi-Tanggal dari CSV
     */
    public function importMulti()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa CSV (.csv). Silakan unduh template yang telah disediakan.');
        }

        $handle = fopen($file->getTempName(), "r");
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membaca file CSV.');
        }

        $header = fgetcsv($handle, 1000, ",");
        $countSuccess = 0;
        $countFailed = 0;

        $this->db->transStart();

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($row) < 5) continue;

            $nis          = trim($row[0]);
            $namaLengkap  = trim($row[1]);
            $tanggal      = trim($row[2]);
            $jenis        = strtolower(trim($row[3])) == 'tarik' ? 'tarik' : 'setor';
            $jumlah       = (float) str_replace(['.', ','], '', trim($row[4]));
            $ket          = isset($row[5]) ? trim($row[5]) : "Import Multi-Tanggal {$tanggal}";

            if (empty($nis) || $jumlah <= 0 || empty($tanggal)) {
                $countFailed++;
                continue;
            }

            $siswa = $this->siswaModel->where('nis', $nis)->first();
            if (!$siswa && !empty($namaLengkap)) {
                $siswa = $this->siswaModel->where('nama_lengkap', $namaLengkap)->first();
            }

            if (!$siswa) {
                $countFailed++;
                continue;
            }

            $saldo_sebelum = (float) $siswa['saldo_akhir'];
            $saldo_sesudah = 0;

            if ($jenis == 'setor') {
                $saldo_sesudah = $saldo_sebelum + $jumlah;
            } else {
                if ($jumlah > $saldo_sebelum) {
                    $countFailed++;
                    continue;
                }
                $saldo_sesudah = $saldo_sebelum - $jumlah;
            }

            $dataTransaksi = [
                'kode_transaksi'  => $this->transaksiModel->generateKodeTransaksi(),
                'siswa_id'        => $siswa['id'],
                'jenis_transaksi' => $jenis,
                'jumlah'          => $jumlah,
                'keterangan'      => $ket,
                'saldo_sebelum'   => $saldo_sebelum,
                'saldo_sesudah'   => $saldo_sesudah,
                'pengguna_id'     => session()->get('id') ?? 1,
                'created_at'      => $tanggal . ' ' . date('H:i:s'),
            ];

            $this->transaksiModel->insert($dataTransaksi);
            $this->siswaModel->update($siswa['id'], ['saldo_akhir' => $saldo_sesudah]);
            $countSuccess++;
        }

        fclose($handle);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses import transaksi karena error database.');
        }

        session()->setFlashdata('success', "Import Transaksi Selesai: {$countSuccess} transaksi berhasil dicatat, {$countFailed} baris dilewati (NIS tidak ditemukan/nominal 0/saldo kurang).");
        return redirect()->to('/transaksi');
    }
}
