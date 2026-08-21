<?php

namespace App\Models;

use CodeIgniter\Model;

class Siswa extends Model
{
    protected $table            = 'siswa'; // Nama tabel di database
    protected $primaryKey       = 'id';    // Primary key dari tabel

    protected $useAutoIncrement = true;

    protected $returnType       = 'array'; // Tipe data yang dikembalikan (bisa 'object' juga)
    protected $useSoftDeletes   = false;   // Tidak menggunakan soft delete

    // Kolom yang diizinkan untuk diisi/diupdate.
    // PENTING: Jangan masukkan 'saldo_akhir' di sini karena saldo akan diupdate melalui transaksi.
    protected $allowedFields    = [
        'nis', 
        'nama_lengkap', 
        'jenis_kelamin', 
        'tanggal_lahir', 
        'alamat', 
        'status_siswa',
         'saldo_akhir'
    ];

    // Menggunakan timestamps (created_at, updated_at) secara otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
