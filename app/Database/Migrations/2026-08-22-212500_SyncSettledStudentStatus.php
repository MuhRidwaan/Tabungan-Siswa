<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncSettledStudentStatus extends Migration
{
    public function up()
    {
        // Update status_siswa to 'lulus' for any student whose balance is 0 and has year-end withdrawal transactions
        $this->db->query("
            UPDATE siswa 
            SET status_siswa = 'lulus' 
            WHERE saldo_akhir = 0 
              AND status_siswa = 'aktif' 
              AND EXISTS (
                SELECT 1 FROM transaksi_tabungan 
                WHERE transaksi_tabungan.siswa_id = siswa.id 
                  AND (transaksi_tabungan.keterangan LIKE '%Akhir Tahun%' OR transaksi_tabungan.keterangan LIKE '%Penarikan Tabungan%')
              )
        ");
    }

    public function down()
    {
        // No action needed for down
    }
}
