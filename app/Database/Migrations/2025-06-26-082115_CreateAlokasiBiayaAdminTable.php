<?php

// app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateAlokasiBiayaAdminTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlokasiBiayaAdminTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaksi_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'persen_guru' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'jumlah_untuk_guru' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'persen_sekolah' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'jumlah_untuk_sekolah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('transaksi_id'); // Satu transaksi hanya punya satu alokasi
        $this->forge->addForeignKey('transaksi_id', 'transaksi_tabungan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('alokasi_biaya_admin');
    }

    public function down()
    {
        $this->forge->dropTable('alokasi_biaya_admin');
    }
}
