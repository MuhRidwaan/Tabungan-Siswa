<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateKelasTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tingkat' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'wali_kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // Jika guru (wali kelas) dihapus, set wali_kelas_id menjadi NULL, jangan hapus kelasnya.
        $this->forge->addForeignKey('wali_kelas_id', 'pengguna', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas');
    }
}
