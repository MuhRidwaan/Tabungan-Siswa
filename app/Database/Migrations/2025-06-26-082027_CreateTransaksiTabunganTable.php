<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateTransaksiTabunganTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiTabunganTable extends Migration
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
            'kode_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['setor', 'tarik'],
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'saldo_sebelum' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'saldo_sesudah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pengguna_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_transaksi');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('pengguna_id', 'pengguna', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('transaksi_tabungan');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi_tabungan');
    }
}
