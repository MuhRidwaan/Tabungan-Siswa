<?php

namespace App\Models;

use CodeIgniter\Model;

class Guru extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi/diupdate
    protected $allowedFields    = [
        'nama_lengkap',
        'username',
        'password',
        'role'
    ];

    // Otomatis hash password sebelum insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function beforeInsert(array $data)
    {
        return $this->hashPassword($data);
    }

    protected function beforeUpdate(array $data)
    {
        return $this->hashPassword($data);
    }

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    // Menggunakan timestamps (created_at, updated_at) secara otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
