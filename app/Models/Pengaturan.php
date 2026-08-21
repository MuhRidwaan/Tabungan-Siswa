<?php

namespace App\Models;

use CodeIgniter\Model;

class Pengaturan extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = ['nama_pengaturan', 'nilai_pengaturan', 'keterangan'];

    // Tidak menggunakan timestamps
    protected $useTimestamps = false;

    /**
     * Mengambil semua pengaturan dan mengubahnya menjadi format key => value.
     * Contoh: ['persen_admin_guru' => '1.0']
     */
    public function getPengaturanAsArray()
    {
        $data = $this->findAll();
        $pengaturan = [];
        foreach ($data as $item) {
            $pengaturan[$item['nama_pengaturan']] = $item['nilai_pengaturan'];
        }
        return $pengaturan;
    }
}
