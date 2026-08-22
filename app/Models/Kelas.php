<?php

namespace App\Models;

use CodeIgniter\Model;

class Kelas extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi/diupdate
    protected $allowedFields    = [
        'nama_kelas',
        'tingkat',
        'wali_kelas_id'
    ];

    /**
     * Mengambil semua data kelas dengan join ke tabel pengguna
     * untuk mendapatkan nama wali kelas dan jumlah total siswa.
     */
    public function getAllKelasWithWali($tahunAjaranId = null)
    {
        $builder = $this->select('kelas.*, pengguna.nama_lengkap as nama_wali_kelas, COUNT(DISTINCT riwayat_kelas_siswa.siswa_id) as total_siswa')
                    ->join('pengguna', 'pengguna.id = kelas.wali_kelas_id', 'left');

        if ($tahunAjaranId) {
            $builder->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.kelas_id = kelas.id AND riwayat_kelas_siswa.tahun_ajaran_id = ' . $this->db->escape($tahunAjaranId), 'left');
        } else {
            $builder->join('riwayat_kelas_siswa', 'riwayat_kelas_siswa.kelas_id = kelas.id', 'left');
        }

        return $builder->groupBy('kelas.id')
                       ->orderBy('kelas.tingkat', 'ASC')
                       ->orderBy('kelas.nama_kelas', 'ASC')
                       ->findAll();
    }


    // Menggunakan timestamps (created_at, updated_at) secara otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
