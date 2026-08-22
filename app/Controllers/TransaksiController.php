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
        $perPage         = $this->request->getGet('per_page') ?: 10;
        $search          = $this->request->getGet('q');
        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');
        $selectedKelasId = $this->request->getGet('kelas_id');

        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $kelasModel       = new \App\Models\Kelas();

        $tahunAjaranList = $tahunAjaranModel->orderBy('id', 'DESC')->findAll();
        $tahunAktif      = $tahunAjaranModel->where('status', 'aktif')->first();

        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $statsBuilder = $this->db->table('transaksi_tabungan')
            ->select('
                COUNT(transaksi_tabungan.id) as total_transaksi,
                COALESCE(SUM(CASE WHEN transaksi_tabungan.jenis_transaksi = "setor" THEN transaksi_tabungan.jumlah ELSE 0 END), 0) as total_setor,
                COALESCE(SUM(CASE WHEN transaksi_tabungan.jenis_transaksi = "tarik" THEN transaksi_tabungan.jumlah ELSE 0 END), 0) as total_tarik
            ');

        if ($selectedTahunId || $selectedKelasId) {
            $statsBuilder->join('siswa', 'siswa.id = transaksi_tabungan.siswa_id', 'left')
                         ->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.siswa_id = siswa.id', 'left');
            if ($selectedTahunId && $selectedTahunId !== 'semua') {
                $statsBuilder->where('riwayat_kelas_siswa.tahun_ajaran_id', $selectedTahunId);
            }
            if ($selectedKelasId && $selectedKelasId !== 'semua') {
                $statsBuilder->where('riwayat_kelas_siswa.kelas_id', $selectedKelasId);
            }
        }

        $statsData = $statsBuilder->get()->getRowArray();
        $statsData['total_kas'] = $statsData['total_setor'] - $statsData['total_tarik'];

        $data = [
            'title'           => 'Riwayat Transaksi Tabungan',
            'transaksi'       => $this->transaksiModel->getTransaksiWithDetails($search, $perPage, $selectedTahunId, $selectedKelasId),
            'pager'           => $this->transaksiModel->pager,
            'perPage'         => $perPage,
            'search'          => $search,
            'siswa'           => $this->siswaModel->where('status_siswa', 'aktif')->findAll(),
            'stats'           => $statsData,
            'tahunAjaran'     => $tahunAjaranList,
            'selectedTahunId' => $selectedTahunId,
            'kelas'           => $kelasModel->findAll(),
            'selectedKelasId' => $selectedKelasId
        ];
        return view('transaksi/index', $data);
    }

    /**
     * Endpoint AJAX untuk menyimpan (Create/Update) transaksi
     */
    public function save()
    {
        // Sanitize numeric input from formatted currency strings
        $rawJumlah = str_replace(['.', ','], '', (string) $this->request->getPost('jumlah'));
        $_POST['jumlah'] = $rawJumlah;

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
            'tanggal_transaksi' => date('Y-m-d H:i:s'),
            'pengguna_id'     => $this->getPenggunaId(),
        ];

        if ($id) { // Update
            // Logika update transaksi bisa jadi kompleks, untuk saat ini kita nonaktifkan
            // $this->transaksiModel->update($id, $dataTransaksi);
            $message = 'Fitur update belum diaktifkan.';
            // return $this->response->setJSON(['success' => false, 'message' => $message]);
        } else { // Create
            $dataTransaksi['kode_transaksi'] = $this->transaksiModel->generateKodeTransaksi();
            $transaksiId = $this->transaksiModel->insert($dataTransaksi);
            $message = 'Transaksi berhasil ditambahkan.';

            $includeAlokasi = (int)$this->request->getPost('include_alokasi');
            if ($includeAlokasi && $transaksiId) {
                $pengaturanModel = new \App\Models\Pengaturan();
                $pengaturan      = $pengaturanModel->getPengaturanAsArray();
                $persenGuru      = isset($pengaturan['persen_admin_guru']) ? (float)$pengaturan['persen_admin_guru'] : 1.0;
                $persenSekolah   = isset($pengaturan['persen_admin_sekolah']) ? (float)$pengaturan['persen_admin_sekolah'] : 1.5;

                $alokGuru    = $jumlah * ($persenGuru / 100);
                $alokSekolah = $jumlah * ($persenSekolah / 100);

                $alokasiModel = new \App\Models\AlokasiBiayaAdmin();
                $alokasiModel->insert([
                    'transaksi_id'         => $transaksiId,
                    'persen_guru'          => $persenGuru,
                    'jumlah_untuk_guru'    => $alokGuru,
                    'persen_sekolah'       => $persenSekolah,
                    'jumlah_untuk_sekolah'  => $alokSekolah,
                    'created_at'           => date('Y-m-d H:i:s')
                ]);
            }
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
        $kelasModel       = new \App\Models\Kelas();
        $tahunAjaranModel = new \App\Models\TahunAjaran();

        $tahunAjaranList = $tahunAjaranModel->orderBy('id', 'DESC')->findAll();
        $tahunAktif      = $tahunAjaranModel->where('status', 'aktif')->first();

        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');
        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $data = [
            'title'           => 'Pencatatan Transaksi Kolektif',
            'kelas'           => $kelasModel->getAllKelasWithWali($selectedTahunId),
            'tahunAktif'      => $tahunAktif,
            'tahunAjaran'     => $tahunAjaranList,
            'selectedTahunId' => $selectedTahunId
        ];
        return view('transaksi/kolektif', $data);
    }

    /**
     * AJAX Endpoint untuk mengambil daftar siswa per kelas pada tahun ajaran aktif
     */
    public function getSiswaByKelas($kelasId = null)
    {
        $siswaList = [];
        $tahunId   = $this->request->getGet('tahun_ajaran_id');

        if ($kelasId && $kelasId !== 'all') {
            $tahunAjaranModel = new \App\Models\TahunAjaran();
            if (!$tahunId) {
                $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();
                $tahunId    = $tahunAktif['id'] ?? null;
            }
            if ($tahunId) {
                $riwayatModel = new \App\Models\RiwayatKelasSiswa();
                $siswaList    = $riwayatModel->getSiswaByKelasTahun($kelasId, $tahunId);
            }
        }

        // Fallback: Jika belum memilih kelas spesifik atau kelasId === 'all'
        if (empty($siswaList) && ($kelasId === 'all' || !$kelasId)) {
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
                'tanggal_transaksi' => $tanggalInput . ' ' . date('H:i:s'),
                'pengguna_id'     => $this->getPenggunaId(),
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
                'tanggal_transaksi' => $tgl . ' ' . date('H:i:s'),
                'pengguna_id'     => $this->getPenggunaId(),
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
     * Download Template Excel Native (.xls) untuk Import Transaksi Multi-Tanggal
     */
    public function downloadTemplateMulti()
    {
        $siswaModel = new \App\Models\Siswa();
        $siswaList  = $siswaModel->where('status_siswa', 'aktif')->orderBy('nama_lengkap', 'ASC')->findAll();

        $filename = "template_import_transaksi_multi_" . date('Ymd_His') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Header">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#0277BD" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="StringText">
   <NumberFormat ss:Format="@"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Form Import Transaksi">
  <Table>
   <Column ss:Width="100"/>
   <Column ss:Width="180"/>
   <Column ss:Width="120"/>
   <Column ss:Width="140"/>
   <Column ss:Width="120"/>
   <Column ss:Width="200"/>
   <Row ss:Height="25">
    <Cell ss:StyleID="Header"><Data ss:Type="String">nis</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">nama_lengkap</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">tanggal</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">jenis_transaksi</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">nominal</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">keterangan</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="StringText"><Data ss:Type="String">1001</Data></Cell>
    <Cell><Data ss:Type="String">Ahmad Dani</Data></Cell>
    <Cell><Data ss:Type="String"><?= date('Y-m-01') ?></Data></Cell>
    <Cell><Data ss:Type="String">setor</Data></Cell>
    <Cell><Data ss:Type="Number">10000</Data></Cell>
    <Cell><Data ss:Type="String">Setoran Tanggal 1</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="StringText"><Data ss:Type="String">1001</Data></Cell>
    <Cell><Data ss:Type="String">Ahmad Dani</Data></Cell>
    <Cell><Data ss:Type="String"><?= date('Y-m-02') ?></Data></Cell>
    <Cell><Data ss:Type="String">setor</Data></Cell>
    <Cell><Data ss:Type="Number">15000</Data></Cell>
    <Cell><Data ss:Type="String">Setoran Tanggal 2</Data></Cell>
   </Row>
  </Table>
 </Worksheet>

 <Worksheet ss:Name="REFERENSI NIS SISWA">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="100"/>
   <Column ss:Width="200"/>
   <Column ss:Width="140"/>
   <Row ss:Height="25">
    <Cell ss:StyleID="Header"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NIS</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Lengkap Siswa</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Saldo Saat Ini (Rp)</Data></Cell>
   </Row>
   <?php foreach ($siswaList as $idx => $s): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $idx + 1 ?></Data></Cell>
    <Cell ss:StyleID="StringText"><Data ss:Type="String"><?= esc($s['nis']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= esc($s['nama_lengkap']) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= (float)$s['saldo_akhir'] ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
</Workbook>
        <?php
        exit;
    }

    /**
     * Import Rekap Transaksi Multi-Tanggal dari Excel / CSV
     */
    public function importMulti()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'xls', 'xlsx', 'xml', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa Excel (.xls) atau CSV (.csv). Silakan unduh template yang telah disediakan.');
        }

        $content = file_get_contents($file->getTempName());
        $rawRows = [];

        if (strpos($content, '<Table') !== false || strpos($content, '<Row') !== false) {
            // Parse Excel XML Spreadsheet
            preg_match_all('/<Row[^>]*>(.*?)<\/Row>/s', $content, $rowMatches);
            foreach ($rowMatches[1] as $rIndex => $rowXml) {
                preg_match_all('/<Data[^>]*>(.*?)<\/Data>/s', $rowXml, $dataMatches);
                if (!empty($dataMatches[1])) {
                    $rawRows[] = array_map(function($val) {
                        return trim(html_entity_decode(strip_tags($val)));
                    }, $dataMatches[1]);
                }
            }
            if (!empty($rawRows) && strtolower($rawRows[0][0] ?? '') == 'nis') {
                array_shift($rawRows);
            }
        } else {
            // Parse standard CSV
            $handle = fopen($file->getTempName(), "r");
            if ($handle) {
                fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rawRows[] = $data;
                }
                fclose($handle);
            }
        }

        $countSuccess = 0;
        $countFailed  = 0;

        $this->db->transStart();

        foreach ($rawRows as $row) {
            if (count($row) < 4) continue;

            $nis          = trim($row[0]);
            $namaLengkap  = trim($row[1] ?? '');
            $tanggal      = trim($row[2] ?? date('Y-m-d'));
            $jenis        = strtolower(trim($row[3] ?? 'setor')) == 'tarik' ? 'tarik' : 'setor';
            $jumlah       = (float) str_replace(['.', ','], '', trim($row[4] ?? '0'));
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
                'tanggal_transaksi' => $tanggal . ' ' . date('H:i:s'),
                'pengguna_id'     => $this->getPenggunaId(),
                'created_at'      => $tanggal . ' ' . date('H:i:s'),
            ];

            $this->transaksiModel->insert($dataTransaksi);
            $this->siswaModel->update($siswa['id'], ['saldo_akhir' => $saldo_sesudah]);
            $countSuccess++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses import transaksi karena error database.');
        }

        session()->setFlashdata('success', "Import Transaksi Selesai: {$countSuccess} transaksi berhasil dicatat, {$countFailed} baris dilewati (NIS tidak ditemukan/nominal 0/saldo kurang).");
        return redirect()->to('/transaksi');
    }

    /**
     * Menampilkan Halaman Penarikan Tabungan Akhir Tahun Ajaran
     */
    public function akhirTahun()
    {
        $kelasModel       = new \App\Models\Kelas();
        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $riwayatModel     = new \App\Models\RiwayatKelasSiswa();

        $tahunAjaranList = $tahunAjaranModel->orderBy('id', 'DESC')->findAll();
        $tahunAktif      = $tahunAjaranModel->where('status', 'aktif')->first();

        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');
        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $selectedKelasId = $this->request->getGet('kelas_id');
        
        $siswaList = [];
        $selectedKelas = null;
        $totalSiswa = 0;
        $countLunas = 0;
        $totalBelumDitarik = 0;
        $isClassLunas = false;
        $selectedTahunInfo = $tahunAjaranModel->find($selectedTahunId);

        if ($selectedKelasId && $selectedTahunId) {
            $selectedKelas = $kelasModel->find($selectedKelasId);
            $siswaList = $riwayatModel->getSiswaByKelasTahun($selectedKelasId, $selectedTahunId);
            $totalSiswa = count($siswaList);

            foreach ($siswaList as $s) {
                $saldo = (float)$s['saldo_akhir'];
                if ($saldo <= 0) {
                    $countLunas++;
                } else {
                    $totalBelumDitarik += $saldo;
                }
            }

            if ($totalSiswa > 0 && $countLunas === $totalSiswa) {
                $isClassLunas = true;
            }
        }

        $pengaturanModel  = new \App\Models\Pengaturan();
        $pengaturan       = $pengaturanModel->getPengaturanAsArray();
        $persenGuru       = isset($pengaturan['persen_admin_guru']) ? (float)$pengaturan['persen_admin_guru'] : 1.0;
        $persenSekolah    = isset($pengaturan['persen_admin_sekolah']) ? (float)$pengaturan['persen_admin_sekolah'] : 1.5;

        $allKelas = $kelasModel->getAllKelasWithWali($selectedTahunId);

        $data = [
            'title'             => 'Penarikan Tabungan Akhir Tahun Ajaran',
            'tahunAjaran'       => $tahunAjaranList,
            'selectedTahunId'   => $selectedTahunId,
            'selectedTahunInfo' => $selectedTahunInfo,
            'tahunAktif'        => $tahunAktif,
            'kelas'             => $allKelas,
            'selectedKelasId'   => $selectedKelasId,
            'selectedKelas'     => $selectedKelas,
            'siswaList'         => $siswaList,
            'totalSiswa'        => $totalSiswa,
            'countLunas'        => $countLunas,
            'totalBelumDitarik' => $totalBelumDitarik,
            'isClassLunas'      => $isClassLunas,
            'persenGuru'        => $persenGuru,
            'persenSekolah'     => $persenSekolah
        ];

        return view('transaksi/akhir_tahun', $data);
    }

    /**
     * AJAX Endpoint untuk menarik lunas saldo 1 siswa di akhir tahun (Saldo -> Rp 0)
     */
    public function saveTarikLunas()
    {
        $siswaId        = $this->request->getPost('siswa_id');
        $tahunId        = $this->request->getPost('tahun_ajaran_id');
        $includeAlokasi = (int)$this->request->getPost('include_alokasi');

        if (!$siswaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Siswa tidak valid.']);
        }

        $siswa = $this->siswaModel->find($siswaId);
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }

        $saldoAwal = (float)$siswa['saldo_akhir'];
        if ($saldoAwal <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Saldo siswa sudah Rp 0 / lunas.']);
        }

        $tahunAjaranModel = new \App\Models\TahunAjaran();
        $tahunInfo        = $tahunAjaranModel->find($tahunId);
        $taNama           = $tahunInfo['nama_tahun_ajaran'] ?? date('Y');

        $pengaturanModel = new \App\Models\Pengaturan();
        $pengaturan      = $pengaturanModel->getPengaturanAsArray();
        $persenGuru      = isset($pengaturan['persen_admin_guru']) ? (float)$pengaturan['persen_admin_guru'] : 1.0;
        $persenSekolah   = isset($pengaturan['persen_admin_sekolah']) ? (float)$pengaturan['persen_admin_sekolah'] : 1.5;

        $alokGuru    = 0;
        $alokSekolah = 0;
        $ket         = "Penarikan Tabungan Akhir Tahun Ajaran {$taNama}";

        if ($includeAlokasi) {
            $alokGuru    = $saldoAwal * ($persenGuru / 100);
            $alokSekolah = $saldoAwal * ($persenSekolah / 100);
            $ket        .= " (Bagi Hasil: Guru Rp " . number_format($alokGuru, 0, ',', '.') . ", Sekolah Rp " . number_format($alokSekolah, 0, ',', '.') . ")";
        }

        $this->db->transStart();

        $dataTransaksi = [
            'kode_transaksi'    => $this->transaksiModel->generateKodeTransaksi(),
            'siswa_id'          => $siswaId,
            'jenis_transaksi'   => 'tarik',
            'jumlah'            => $saldoAwal,
            'keterangan'        => $ket,
            'saldo_sebelum'     => $saldoAwal,
            'saldo_sesudah'     => 0,
            'tanggal_transaksi' => date('Y-m-d H:i:s'),
            'pengguna_id'       => $this->getPenggunaId(),
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        $transaksiId = $this->transaksiModel->insert($dataTransaksi);
        $this->siswaModel->update($siswaId, ['saldo_akhir' => 0]);

        if ($includeAlokasi && $transaksiId) {
            $alokasiModel = new \App\Models\AlokasiBiayaAdmin();
            $alokasiModel->insert([
                'transaksi_id'         => $transaksiId,
                'persen_guru'          => $persenGuru,
                'jumlah_untuk_guru'    => $alokGuru,
                'persen_sekolah'       => $persenSekolah,
                'jumlah_untuk_sekolah'  => $alokSekolah,
                'created_at'           => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses penarikan akhir tahun.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Penarikan akhir tahun sebesar Rp " . number_format($saldoAwal, 0, ',', '.') . " untuk " . esc($siswa['nama_lengkap']) . " berhasil diproses (Saldo menjadi Rp 0)."
        ]);
    }

    /**
     * AJAX Endpoint untuk set status seluruh siswa kelas menjadi 'lulus'
     */
    public function setLulusKelas()
    {
        $kelasId = $this->request->getPost('kelas_id');
        $tahunId = $this->request->getPost('tahun_ajaran_id');

        if (!$kelasId || !$tahunId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter kelas atau tahun ajaran tidak valid.']);
        }

        $riwayatModel = new \App\Models\RiwayatKelasSiswa();
        $siswaInKelas = $riwayatModel->getSiswaByKelasTahun($kelasId, $tahunId);

        if (empty($siswaInKelas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada siswa pada kelas ini.']);
        }

        $countUpdated = 0;
        foreach ($siswaInKelas as $s) {
            $sId = $s['siswa_id'];
            $this->siswaModel->update($sId, ['status_siswa' => 'lulus']);
            $countUpdated++;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Status kelulusan berhasil diperbarui! {$countUpdated} siswa telah diset menjadi LULUS."
        ]);
    }

    /**
     * Helper privat untuk mendapatkan ID pengguna (petugas/guru/admin) yang sedang login.
     */
    private function getPenggunaId()
    {
        if (session()->get('id')) {
            return session()->get('id');
        }
        if (session()->get('pengguna_id')) {
            return session()->get('pengguna_id');
        }
        if (function_exists('auth') && auth()->user()) {
            $username = auth()->user()->username;
            $penggunaModel = new \App\Models\Guru();
            $p = $penggunaModel->where('username', $username)->first();
            if ($p) {
                return $p['id'];
            }
        }
        $firstPengguna = (new \App\Models\Guru())->first();
        return $firstPengguna['id'] ?? 1;
    }
}
