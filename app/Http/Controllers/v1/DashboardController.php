<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiBank;
use App\Models\TransaksiKas;
use App\Models\UserActivity;

class DashboardController extends Controller
{

    public function index()
    {
        $month = date('m');
        $year = date('Y');

        $jumlahKasMasuk = TransaksiKas::where('tipe', 'Masuk')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('total');
        $jumlahKasKeluar = TransaksiKas::where('tipe', 'Keluar')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('total');
        $jumlahBankMasuk = TransaksiBank::where('tipe', 'Masuk')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('total');
        $jumlahBankKeluar = TransaksiBank::where('tipe', 'Keluar')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('total');

        $transaksiKas = TransaksiKas::with('kodeAkun')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->orderBy('tanggal', 'DESC')->take(10)->get();
        $transaksiBank = TransaksiBank::with('kodeAkun')->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->orderBy('tanggal', 'DESC')->take(10)->get();

        $latestActivity = UserActivity::orderBy('created_at', 'DESC')->take(10)->get();

        return view('dashboard', compact('transaksiKas', 'transaksiBank', 'jumlahKasMasuk', 'jumlahKasKeluar', 'jumlahBankMasuk', 'jumlahBankKeluar', 'latestActivity'));
    }
}
