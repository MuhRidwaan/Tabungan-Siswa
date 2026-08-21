<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateTahunAjaranTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjaranTable extends Migration
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
            'tahun_mulai' => [
                'type' => 'YEAR',
            ],
            'tahun_selesai' => [
                'type' => 'YEAR',
            ],
            'nama_tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'tidak aktif'],
                'default'    => 'tidak aktif',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tahun_ajaran');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_ajaran');
    }
}
