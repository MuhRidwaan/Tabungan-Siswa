<?php

// app/Database/Seeds/InitialDataSeeder.php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Panggil semua method seeder
        $this->seedPengguna();
        $this->seedPengaturan();
        $this->seedTahunAjaran();
        $this->seedKelas();
        $this->seedSiswaDanRiwayat();
    }

    private function seedPengguna()
    {
        // 1. Menambahkan Pengguna Admin ke tabel pengguna
        $this->db->table('pengguna')->insert([
            'id'           => 1,
            'nama_lengkap' => 'Administrator',
            'username'     => 'admin',
            'password'     => password_hash('admin123', PASSWORD_DEFAULT),
            'role'         => 'admin',
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // 2. Menambahkan Pengguna Guru ke tabel pengguna
        $guruData = [
            [
                'id'           => 2,
                'nama_lengkap' => 'Budi Setiawan, S.Pd.',
                'username'     => 'guru_budi',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'role'         => 'guru',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'id'           => 3,
                'nama_lengkap' => 'Citra Lestari, S.Kom.',
                'username'     => 'guru_citra',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'role'         => 'guru',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('pengguna')->insertBatch($guruData);

        // 3. Menambahkan Pengguna ke Shield Auth (tabel users & auth_identities)
        $userModel = new \CodeIgniter\Shield\Models\UserModel();

        $superAdminUser = new \CodeIgniter\Shield\Entities\User([
            'username' => 'superadmin',
            'email'    => 'superadmin@gmail.com',
            'password' => 'admin123',
        ]);
        $userModel->save($superAdminUser);
        $savedSuperAdmin = $userModel->findById($userModel->getInsertID());
        if ($savedSuperAdmin) {
            $savedSuperAdmin->addGroup('admin');
        }
        
        $adminUser = new \CodeIgniter\Shield\Entities\User([
            'username' => 'admin',
            'email'    => 'admin@admin.com',
            'password' => 'admin123',
        ]);
        $userModel->save($adminUser);
        $savedAdmin = $userModel->findById($userModel->getInsertID());
        if ($savedAdmin) {
            $savedAdmin->addGroup('admin');
        }

        $guruUser1 = new \CodeIgniter\Shield\Entities\User([
            'username' => 'guru_budi',
            'email'    => 'budi@guru.com',
            'password' => 'admin123',
        ]);
        $userModel->save($guruUser1);

        $guruUser2 = new \CodeIgniter\Shield\Entities\User([
            'username' => 'guru_citra',
            'email'    => 'citra@guru.com',
            'password' => 'admin123',
        ]);
        $userModel->save($guruUser2);
    }

    private function seedPengaturan()
    {
        $pengaturanData = [
            [
                'nama_pengaturan'  => 'persen_admin_guru',
                'nilai_pengaturan' => '1.0',
                'keterangan'       => 'Persentase biaya admin dari setoran untuk dialokasikan ke guru.',
            ],
            [
                'nama_pengaturan'  => 'persen_admin_sekolah',
                'nilai_pengaturan' => '1.5',
                'keterangan'       => 'Persentase biaya admin dari setoran untuk dialokasikan ke kas sekolah.',
            ],
        ];
        $this->db->table('pengaturan')->insertBatch($pengaturanData);
    }

    private function seedTahunAjaran()
    {
        $this->db->table('tahun_ajaran')->insert([
            'id'                => 1, // Tetapkan ID
            'tahun_mulai'       => '2024',
            'tahun_selesai'     => '2025',
            'nama_tahun_ajaran' => '2024/2025',
            'status'            => 'aktif',
        ]);
    }

    private function seedKelas()
    {
        $kelasData = [
            [
                'id'            => 1, // Tetapkan ID
                'nama_kelas'    => 'Kelas 10-A',
                'tingkat'       => 10,
                'wali_kelas_id' => 2, // ID Guru Budi
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 2, // Tetapkan ID
                'nama_kelas'    => 'Kelas 11-B',
                'tingkat'       => 11,
                'wali_kelas_id' => 3, // ID Guru Citra
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('kelas')->insertBatch($kelasData);
    }
    
    private function seedSiswaDanRiwayat()
    {
        // Gunakan Faker untuk data siswa yang lebih realistis
        $faker = Factory::create('id_ID');

        // Siswa untuk Kelas 10-A
        for ($i = 1; $i <= 3; $i++) {
            $siswa_id = $this->db->table('siswa')->insert([
                'nis'           => '100' . $i,
                'nama_lengkap'  => $faker->name,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tanggal_lahir' => $faker->date(),
                'alamat'        => $faker->address,
                'saldo_akhir'   => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            
            // Masukkan ke riwayat kelas
            $this->db->table('riwayat_kelas_siswa')->insert([
                'siswa_id'        => $siswa_id,
                'kelas_id'        => 1, // Kelas 10-A
                'tahun_ajaran_id' => 1, // Tahun Ajaran 2024/2025
            ]);
        }

        // Siswa untuk Kelas 11-B
        for ($i = 4; $i <= 6; $i++) {
            $siswa_id = $this->db->table('siswa')->insert([
                'nis'           => '100' . $i,
                'nama_lengkap'  => $faker->name,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tanggal_lahir' => $faker->date(),
                'alamat'        => $faker->address,
                'saldo_akhir'   => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            // Masukkan ke riwayat kelas
            $this->db->table('riwayat_kelas_siswa')->insert([
                'siswa_id'        => $siswa_id,
                'kelas_id'        => 2, // Kelas 11-B
                'tahun_ajaran_id' => 1, // Tahun Ajaran 2024/2025
            ]);
        }
    }
}
