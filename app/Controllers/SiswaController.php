<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelasSiswa;

class SiswaController extends BaseController
{
    protected $siswa;
    protected $kelasModel;
    protected $tahunAjaranModel;
    protected $riwayatKelasModel;
    protected $db;

    public function __construct()
    {
        $this->siswa = new Siswa();
        $this->kelasModel = new Kelas();
        $this->tahunAjaranModel = new TahunAjaran();
        $this->riwayatKelasModel = new RiwayatKelasSiswa();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman daftar siswa (Read)
     */
    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?: 10;
        $search  = $this->request->getGet('q');
        $selectedKelasId = $this->request->getGet('kelas_id');
        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');

        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $builder = $this->siswa->select('siswa.*, kelas.nama_kelas, tahun_ajaran.nama_tahun_ajaran, riwayat_kelas_siswa.kelas_id')
                              ->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.siswa_id = siswa.id AND riwayat_kelas_siswa.tahun_ajaran_id = ' . $this->db->escape($selectedTahunId), 'left')
                              ->join('kelas', 'kelas.id = riwayat_kelas_siswa.kelas_id', 'left')
                              ->join('tahun_ajaran', 'tahun_ajaran.id = riwayat_kelas_siswa.tahun_ajaran_id', 'left');

        if ($selectedKelasId) {
            $builder->where('riwayat_kelas_siswa.kelas_id', $selectedKelasId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('siswa.nama_lengkap', $search)
                    ->orLike('siswa.nis', $search)
                    ->groupEnd();
        }

        // Hitung Statistik Ringkasan Siswa (Filtered)
        $statsBuilder = $this->db->table('siswa')
            ->select('
                COUNT(siswa.id) as total_siswa,
                COALESCE(SUM(siswa.saldo_akhir), 0) as total_saldo,
                COUNT(CASE WHEN siswa.jenis_kelamin = "L" THEN 1 END) as total_laki,
                COUNT(CASE WHEN siswa.jenis_kelamin = "P" THEN 1 END) as total_perempuan
            ')
            ->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.siswa_id = siswa.id AND riwayat_kelas_siswa.tahun_ajaran_id = ' . $this->db->escape($selectedTahunId), 'left');

        if ($selectedKelasId) {
            $statsBuilder->where('riwayat_kelas_siswa.kelas_id', $selectedKelasId);
        }

        if ($search) {
            $statsBuilder->groupStart()
                         ->like('siswa.nama_lengkap', $search)
                         ->orLike('siswa.nis', $search)
                         ->groupEnd();
        }

        $statsData = $statsBuilder->get()->getRowArray();

        $data = [
            'title'           => 'Daftar Data Siswa',
            'siswa'           => $builder->paginate($perPage),
            'pager'           => $this->siswa->pager,
            'perPage'         => $perPage,
            'search'          => $search,
            'tahunAjaran'     => $this->tahunAjaranModel->orderBy('tahun_mulai', 'DESC')->findAll(),
            'kelas'           => $this->kelasModel->orderBy('tingkat', 'ASC')->findAll(),
            'selectedTahunId' => $selectedTahunId,
            'selectedKelasId' => $selectedKelasId,
            'tahunAktif'      => $tahunAktif,
            'stats'           => $statsData
        ];

        return view('siswa/index', $data);
    }

    /**
     * Menampilkan form untuk menambah siswa baru
     */
    public function new()
    {
        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        $data = [
            'title'      => 'Tambah Siswa Baru',
            'validation' => \Config\Services::validation(),
            'kelas'      => $this->kelasModel->orderBy('tingkat', 'ASC')->findAll(),
            'tahunAktif' => $tahunAktif
        ];
        return view('siswa/create', $data);
    }

    /**
     * Menyimpan data siswa baru ke database (Create)
     */
    public function create()
    {
        $rules = [
            'nis'           => 'required|is_unique[siswa.nis]|max_length[20]',
            'nama_lengkap'  => 'required|max_length[100]',
            'jenis_kelamin' => 'required',
            'status_siswa'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->db->transStart();

        $siswaId = $this->siswa->insert([
            'nis'           => $this->request->getPost('nis'),
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'alamat'        => $this->request->getPost('alamat'),
            'status_siswa'  => $this->request->getPost('status_siswa'),
            'saldo_akhir'   => 0
        ]);

        $kelasId = $this->request->getPost('kelas_id');
        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();

        if ($kelasId && $tahunAktif) {
            $this->riwayatKelasModel->insert([
                'siswa_id'        => $siswaId,
                'kelas_id'        => $kelasId,
                'tahun_ajaran_id' => $tahunAktif['id'],
            ]);
        }

        $this->db->transComplete();

        session()->setFlashdata('success', 'Data siswa berhasil ditambahkan.');
        return redirect()->to('/siswa');
    }

    /**
     * Menampilkan form untuk mengedit data siswa
     */
    public function edit($id)
    {
        $siswa = $this->siswa->find($id);
        if (!$siswa) {
            return redirect()->to('/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        $currentRiwayat = null;
        if ($tahunAktif) {
            $currentRiwayat = $this->riwayatKelasModel->where('siswa_id', $id)
                                                      ->where('tahun_ajaran_id', $tahunAktif['id'])
                                                      ->first();
        }

        $data = [
            'title'          => 'Edit Data Siswa',
            'validation'     => \Config\Services::validation(),
            'siswa'          => $siswa,
            'kelas'          => $this->kelasModel->orderBy('tingkat', 'ASC')->findAll(),
            'tahunAktif'     => $tahunAktif,
            'currentKelasId' => $currentRiwayat ? $currentRiwayat['kelas_id'] : null
        ];

        return view('siswa/edit', $data);
    }

    /**
     * Mengupdate data siswa di database (Update)
     */
    public function update($id)
    {
        $rules = [
            'nis'           => "required|is_unique[siswa.nis,id,{$id}]|max_length[20]",
            'nama_lengkap'  => 'required|max_length[100]',
            'jenis_kelamin' => 'required',
            'status_siswa'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->db->transStart();

        $this->siswa->update($id, [
            'nis'           => $this->request->getPost('nis'),
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'alamat'        => $this->request->getPost('alamat'),
            'status_siswa'  => $this->request->getPost('status_siswa'),
        ]);

        $kelasId = $this->request->getPost('kelas_id');
        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();

        if ($kelasId && $tahunAktif) {
            $existing = $this->riwayatKelasModel->where('siswa_id', $id)
                                                ->where('tahun_ajaran_id', $tahunAktif['id'])
                                                ->first();
            if ($existing) {
                $this->riwayatKelasModel->update($existing['id'], ['kelas_id' => $kelasId]);
            } else {
                $this->riwayatKelasModel->insert([
                    'siswa_id'        => $id,
                    'kelas_id'        => $kelasId,
                    'tahun_ajaran_id' => $tahunAktif['id']
                ]);
            }
        }

        $this->db->transComplete();

        session()->setFlashdata('success', 'Data siswa berhasil diupdate.');
        return redirect()->to('/siswa');
    }

    /**
     * Menghapus data siswa (Delete)
     */
    public function delete($id)
    {
        $this->siswa->delete($id);
        session()->setFlashdata('success', 'Data siswa berhasil dihapus.');
        return redirect()->to('/siswa');
    }

    /**
     * Download Template Upload Native Microsoft Excel (.xls) Multi-Sheet
     * Sheet 1: Form Import Siswa
     * Sheet 2: REFERENSI KELAS (PETUNJUK)
     */
    public function downloadTemplate()
    {
        $filename = "template_import_siswa.xls";
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $kelases = $this->kelasModel->orderBy('tingkat', 'ASC')->findAll();
        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();

        echo '<?xml version="1.0"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="HeaderStyle">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#2E7D32" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="HeaderRefStyle">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#0277BD" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="TitleStyle">
   <Font ss:FontName="Calibri" ss:Size="13" ss:Bold="1" ss:Color="#0277BD"/>
  </Style>
 </Styles>

 <!-- TAB 1: FORM TEMPLATE -->
 <Worksheet ss:Name="Form Import Siswa">
  <Table>
   <Row ss:Height="25" ss:StyleID="HeaderStyle">
    <Cell><Data ss:Type="String">nis</Data></Cell>
    <Cell><Data ss:Type="String">nama_lengkap</Data></Cell>
    <Cell><Data ss:Type="String">jenis_kelamin</Data></Cell>
    <Cell><Data ss:Type="String">tanggal_lahir</Data></Cell>
    <Cell><Data ss:Type="String">alamat</Data></Cell>
    <Cell><Data ss:Type="String">nama_kelas</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">1001</Data></Cell>
    <Cell><Data ss:Type="String">Ahmad Dani</Data></Cell>
    <Cell><Data ss:Type="String">L</Data></Cell>
    <Cell><Data ss:Type="String">2010-05-15</Data></Cell>
    <Cell><Data ss:Type="String">Jl. Merdeka No. 10</Data></Cell>
    <Cell><Data ss:Type="String"><?= !empty($kelases[0]) ? esc($kelases[0]['nama_kelas']) : 'Kelas 1' ?></Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">1002</Data></Cell>
    <Cell><Data ss:Type="String">Siti Rahma</Data></Cell>
    <Cell><Data ss:Type="String">P</Data></Cell>
    <Cell><Data ss:Type="String">2010-08-20</Data></Cell>
    <Cell><Data ss:Type="String">Jl. Mawar No. 5</Data></Cell>
    <Cell><Data ss:Type="String"><?= !empty($kelases[0]) ? esc($kelases[0]['nama_kelas']) : 'Kelas 1' ?></Data></Cell>
   </Row>
  </Table>
 </Worksheet>

 <!-- TAB 2: DAFTAR REFERENSI KELAS -->
 <Worksheet ss:Name="REFERENSI KELAS (PETUNJUK)">
  <Table>
   <Row ss:Height="25" ss:StyleID="TitleStyle">
    <Cell><Data ss:Type="String">DAFTAR NAMA KELAS VALID (Tahun Ajaran: <?= esc($tahunAktif['nama_tahun_ajaran'] ?? 'Aktif') ?>)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell><Data ss:Type="String">Petunjuk: Salin (Copy-Paste) Nama Kelas dari daftar di bawah ke kolom nama_kelas pada Tab 1.</Data></Cell>
   </Row>
   <Row ss:Height="25" ss:StyleID="HeaderRefStyle">
    <Cell><Data ss:Type="String">No</Data></Cell>
    <Cell><Data ss:Type="String">Nama Kelas (Salin Teks Ini)</Data></Cell>
    <Cell><Data ss:Type="String">Tingkat</Data></Cell>
    <Cell><Data ss:Type="String">Wali Kelas</Data></Cell>
   </Row>
   <?php foreach ($kelases as $idx => $k) : ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $idx + 1 ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= esc($k['nama_kelas']) ?></Data></Cell>
    <Cell><Data ss:Type="String">Tingkat <?= esc($k['tingkat']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= esc($k['nama_wali'] ?? 'Belum Ada Wali') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
</Workbook>
        <?php
        exit;
    }

    /**
     * Import Data Siswa dari File Excel (.xls, .xlsx, .csv)
     */
    public function import()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xls', 'xlsx', 'csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa Excel (.xls) atau CSV (.csv). Silakan unduh template yang disediakan.');
        }

        $filePath = $file->getTempName();
        $content = file_get_contents($filePath);
        $rows = [];

        // 1. Parsing Excel XML Format (<Worksheet ss:Name="Form Import Siswa"> or <Row>)
        if (strpos($content, '<Row') !== false || strpos($content, '<tr') !== false) {
            // Check for XML Row elements
            if (strpos($content, '<Row') !== false) {
                // Isolate first worksheet if multi-sheet XML
                if (strpos($content, '<Worksheet') !== false) {
                    preg_match('/<Worksheet[^>]*>(.*?)<\/Worksheet>/is', $content, $sheetMatch);
                    if (!empty($sheetMatch[1])) {
                        $content = $sheetMatch[1];
                    }
                }
                preg_match_all('/<Row[^>]*>(.*?)<\/Row>/is', $content, $rowMatches);
                if (!empty($rowMatches[1])) {
                    foreach ($rowMatches[1] as $idx => $rXml) {
                        if ($idx === 0) continue; // Skip header
                        preg_match_all('/<Data[^>]*>(.*?)<\/Data>/is', $rXml, $cellMatches);
                        if (!empty($cellMatches[1])) {
                            $row = array_map(function($val) {
                                return trim(strip_tags($val));
                            }, $cellMatches[1]);
                            $rows[] = $row;
                        }
                    }
                }
            } else {
                // HTML Table format
                preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $content, $trMatches);
                if (!empty($trMatches[1])) {
                    foreach ($trMatches[1] as $idx => $tr) {
                        if ($idx === 0) continue; // Skip header
                        preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $tr, $tdMatches);
                        if (!empty($tdMatches[1])) {
                            $row = array_map(function($val) {
                                return trim(strip_tags($val));
                            }, $tdMatches[1]);
                            $rows[] = $row;
                        }
                    }
                }
            }
        } else {
            // Parsing CSV / TXT / delimiter
            $lines = explode("\n", str_replace("\r", "", $content));
            $delimiter = (strpos($content, "\t") !== false) ? "\t" : ((strpos($content, ";") !== false) ? ";" : ",");
            foreach ($lines as $idx => $line) {
                if ($idx === 0 || trim($line) === '') continue; // Skip header or empty
                $row = str_getcsv($line, $delimiter);
                $rows[] = $row;
            }
        }

        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        $countSuccess = 0;
        $countFailed = 0;

        $this->db->transStart();

        foreach ($rows as $row) {
            if (count($row) < 2) continue;

            $nis          = trim($row[0]);
            $namaLengkap  = trim($row[1]);
            $jenisKelamin = isset($row[2]) && strtoupper(trim($row[2])) == 'P' ? 'P' : 'L';
            $tanggalLahir = isset($row[3]) && !empty($row[3]) ? trim($row[3]) : null;
            $alamat       = isset($row[4]) ? trim($row[4]) : '';
            $namaKelas    = isset($row[5]) ? trim($row[5]) : '';

            if (empty($nis) || empty($namaLengkap)) continue;

            $existingSiswa = $this->siswa->where('nis', $nis)->first();
            if ($existingSiswa) {
                $countFailed++;
                continue;
            }

            $siswaId = $this->siswa->insert([
                'nis'           => $nis,
                'nama_lengkap'  => $namaLengkap,
                'jenis_kelamin' => $jenisKelamin,
                'tanggal_lahir' => $tanggalLahir,
                'alamat'        => $alamat,
                'status_siswa'  => 'aktif',
                'saldo_akhir'   => 0
            ]);

            if (!empty($namaKelas) && $tahunAktif) {
                $kelasRow = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
                if ($kelasRow) {
                    $this->riwayatKelasModel->insert([
                        'siswa_id'        => $siswaId,
                        'kelas_id'        => $kelasRow['id'],
                        'tahun_ajaran_id' => $tahunAktif['id']
                    ]);
                }
            }

            $countSuccess++;
        }

        $this->db->transComplete();

        session()->setFlashdata('success', "Import Data Siswa Selesai: {$countSuccess} siswa berhasil didaftarkan, {$countFailed} dilewati (NIS ganda/sudah ada).");
        return redirect()->to('/siswa');
    }

    /**
     * Export Data Siswa ke File CSV
     */
    public function export()
    {
        $selectedKelasId = $this->request->getGet('kelas_id');
        $selectedTahunId = $this->request->getGet('tahun_ajaran_id');

        $tahunAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        if (!$selectedTahunId && $tahunAktif) {
            $selectedTahunId = $tahunAktif['id'];
        }

        $builder = $this->siswa->select('siswa.nis, siswa.nama_lengkap, siswa.jenis_kelamin, siswa.tanggal_lahir, siswa.alamat, siswa.status_siswa, siswa.saldo_akhir, kelas.nama_kelas')
                              ->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.siswa_id = siswa.id AND riwayat_kelas_siswa.tahun_ajaran_id = ' . $this->db->escape($selectedTahunId), 'left')
                              ->join('kelas', 'kelas.id = riwayat_kelas_siswa.kelas_id', 'left');

        if ($selectedKelasId) {
            $builder->where('riwayat_kelas_siswa.kelas_id', $selectedKelasId);
        }

        $list = $builder->findAll();

        $filename = "data_siswa_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['NIS', 'Nama Lengkap', 'Jenis Kelamin', 'Tanggal Lahir', 'Alamat', 'Kelas', 'Status', 'Saldo Tabungan (Rp)']);

        foreach ($list as $s) {
            fputcsv($output, [
                $s['nis'],
                $s['nama_lengkap'],
                $s['jenis_kelamin'],
                $s['tanggal_lahir'],
                $s['alamat'],
                $s['nama_kelas'] ?? 'Belum Ditempatkan',
                ucfirst($s['status_siswa']),
                number_format($s['saldo_akhir'], 0, ',', '.')
            ]);
        }

        fclose($output);
        exit;
    }
}
