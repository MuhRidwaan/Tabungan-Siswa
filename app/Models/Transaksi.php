<?php

namespace App\Models;

use CodeIgniter\Model;

class Transaksi extends Model
{
    protected $table            = 'transaksi_tabungan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'kode_transaksi',
        'siswa_id',
        'jenis_transaksi',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'pengguna_id'
    ];

    // Menggunakan created_at saja
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Tidak ada updated_at

    /**
     * Mengambil data transaksi dengan join dan pagination.
     */
    public function getTransaksiWithDetails($search = null, $perPage = 10)
    {
        $builder = $this->select('transaksi_tabungan.*, siswa.nis, siswa.nama_lengkap as nama_siswa, pengguna.nama_lengkap as nama_pengguna')
                        ->join('siswa', 'siswa.id = transaksi_tabungan.siswa_id', 'left')
                        ->join('pengguna', 'pengguna.id = transaksi_tabungan.pengguna_id', 'left');
        
        // Fitur pencarian
        if ($search) {
            $builder->groupStart()
                    ->like('siswa.nama_lengkap', $search)
                    ->orLike('siswa.nis', $search)
                    ->orLike('transaksi_tabungan.kode_transaksi', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('transaksi_tabungan.created_at', 'DESC')
                       ->paginate($perPage);
    }

    /**
     * Menghasilkan kode transaksi unik
     */
    public function generateKodeTransaksi()
    {
        // Format: TRX-YYYYMMDD-XXXX
        $date = date('Ymd');
        $last_transaksi = $this->like('kode_transaksi', "TRX-{$date}-", 'after')->orderBy('kode_transaksi', 'DESC')->first();
        
        $last_number = 0;
        if ($last_transaksi) {
            $last_number = (int) substr($last_transaksi['kode_transaksi'], -4);
        }
        
        $new_number = $last_number + 1;
        $kode_transaksi = "TRX-{$date}-" . str_pad($new_number, 4, '0', STR_PAD_LEFT);

        return $kode_transaksi;
    }

     public function getLaporanPerSiswa($siswaId, $startDate, $endDate)
    {
        return $this->where('siswa_id', $siswaId)
                    ->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
