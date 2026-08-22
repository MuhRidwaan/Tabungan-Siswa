<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjaranIdToKelas extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('kelas');
        if (!in_array('tahun_ajaran_id', $fields)) {
            $this->forge->addColumn('kelas', [
                'tahun_ajaran_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'wali_kelas_id'
                ]
            ]);
        }

        // Set default tahun_ajaran_id for existing kelas rows
        $tahunAktif = $this->db->table('tahun_ajaran')->where('status', 'aktif')->get()->getRowArray();
        $defaultTahunId = $tahunAktif ? $tahunAktif['id'] : 1;
        $this->db->table('kelas')->where('tahun_ajaran_id IS NULL')->update(['tahun_ajaran_id' => $defaultTahunId]);
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('kelas');
        if (in_array('tahun_ajaran_id', $fields)) {
            $this->forge->dropColumn('kelas', 'tahun_ajaran_id');
        }
    }
}
