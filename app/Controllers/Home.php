<?php

namespace App\Controllers;

use App\Models\Siswa;
use App\Models\Transaksi;
use App\Models\TahunAjaran;

class Home extends BaseController
{
    public function index(): string
    {
        $siswaModel = new Siswa();
        $transaksiModel = new Transaksi();
        $tahunAjaranModel = new TahunAjaran();

        $totalSiswa = $siswaModel->where('status_siswa', 'aktif')->countAllResults();

        $saldoRow = $siswaModel->selectSum('saldo_akhir')->first();
        $totalSaldo = $saldoRow['saldo_akhir'] ?? 0;

        $currentMonth = date('m');
        $currentYear  = date('Y');

        $setorRow = $transaksiModel->where('jenis_transaksi', 'setor')
                                   ->where('MONTH(created_at)', $currentMonth)
                                   ->where('YEAR(created_at)', $currentYear)
                                   ->selectSum('jumlah')->first();
        $totalSetorBulanIni = $setorRow['jumlah'] ?? 0;

        $tarikRow = $transaksiModel->where('jenis_transaksi', 'tarik')
                                   ->where('MONTH(created_at)', $currentMonth)
                                   ->where('YEAR(created_at)', $currentYear)
                                   ->selectSum('jumlah')->first();
        $totalTarikBulanIni = $tarikRow['jumlah'] ?? 0;

        $tahunAktif = $tahunAjaranModel->where('status', 'aktif')->first();
        $recentTransaksi = $transaksiModel->getTransaksiWithDetails(null, 5);

        $data = [
            'title'               => 'Dashboard Tabungan Siswa',
            'totalSiswa'          => $totalSiswa,
            'totalSaldo'          => $totalSaldo,
            'totalSetorBulanIni'  => $totalSetorBulanIni,
            'totalTarikBulanIni'  => $totalTarikBulanIni,
            'tahunAktif'          => $tahunAktif,
            'recentTransaksi'     => $recentTransaksi
        ];

        return view('dashboard', $data);
    }
}
