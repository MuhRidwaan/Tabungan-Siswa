<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatKelasSiswa extends Model
{
    protected $table            = 'riwayat_kelas_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['siswa_id', 'kelas_id', 'tahun_ajaran_id', 'keterangan'];

    // Kita tidak menggunakan timestamps di sini karena waktu pembuatan tidak terlalu relevan
    protected $useTimestamps = false;

    /**
     * Mengambil daftar siswa yang ada di kelas tertentu pada tahun ajaran tertentu.
     */
    public function getSiswaByKelasTahun($kelasId, $tahunAjaranId)
    {
        return $this->select('riwayat_kelas_siswa.id as riwayat_id, riwayat_kelas_siswa.siswa_id, riwayat_kelas_siswa.kelas_id, riwayat_kelas_siswa.tahun_ajaran_id, siswa.nis, siswa.nama_lengkap, siswa.saldo_akhir')
            ->join('siswa', 'siswa.id = riwayat_kelas_siswa.siswa_id')
            ->where('riwayat_kelas_siswa.kelas_id', $kelasId)
            ->where('riwayat_kelas_siswa.tahun_ajaran_id', $tahunAjaranId)
            ->where('siswa.status_siswa', 'aktif')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();
    }
}
