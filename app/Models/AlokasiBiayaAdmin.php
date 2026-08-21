<?php

namespace App\Models;

use CodeIgniter\Model;

class AlokasiBiayaAdmin extends Model
{
    protected $table            = 'alokasi_biaya_admin';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['transaksi_id', 'persen_guru', 'jumlah_untuk_guru', 'persen_sekolah', 'jumlah_untuk_sekolah'];
    
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // Tidak ada kolom updated_at

    /**
     * Mengambil rekap total pemasukan dalam rentang tanggal.
     */
    public function getLaporanPemasukan($startDate, $endDate)
    {
        return $this->select('SUM(jumlah_untuk_guru) as total_guru, SUM(jumlah_untuk_sekolah) as total_sekolah')
                    ->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59')
                    ->get()
                    ->getRowArray();
    }

    /**
     * Mengambil rincian pemasukan dalam rentang tanggal.
     */
    public function getDetailPemasukan($startDate, $endDate)
    {
        return $this->select('alokasi_biaya_admin.*, transaksi_tabungan.kode_transaksi, transaksi_tabungan.created_at as tanggal_transaksi')
                    ->join('transaksi_tabungan', 'transaksi_tabungan.id = alokasi_biaya_admin.transaksi_id')
                    ->where('alokasi_biaya_admin.created_at >=', $startDate . ' 00:00:00')
                    ->where('alokasi_biaya_admin.created_at <=', $endDate . ' 23:59:59')
                    ->orderBy('alokasi_biaya_admin.created_at', 'DESC')
                    ->findAll();
    }
}
