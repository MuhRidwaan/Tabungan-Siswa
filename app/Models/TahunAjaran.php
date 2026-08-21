<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaran extends Model
{
    protected $table            = 'tahun_ajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'tahun_mulai',
        'tahun_selesai',
        'nama_tahun_ajaran',
        'status'
    ];

    // Kita tidak menggunakan timestamps di tabel ini, jadi set ke false
    protected $useTimestamps = false;
}
