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
     * untuk mendapatkan nama wali kelas.
     */
    public function getAllKelasWithWali()
    {
        return $this->select('kelas.*, pengguna.nama_lengkap as nama_wali_kelas')
                    ->join('pengguna', 'pengguna.id = kelas.wali_kelas_id', 'left') // LEFT JOIN agar kelas tanpa wali tetap tampil
                    ->orderBy('kelas.tingkat', 'ASC')
                    ->orderBy('kelas.nama_kelas', 'ASC')
                    ->findAll();
    }


    // Menggunakan timestamps (created_at, updated_at) secara otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
