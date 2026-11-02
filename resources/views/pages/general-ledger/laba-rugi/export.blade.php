<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Laba Rugi</b></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Periode {{ $month . ' - ' . $year }}</b></th>
        </tr>
        <tr>
            <th colspan="2"></th>
        </tr>
    </thead>
</table>
<table border="1">
    @php
        $totalPenjualan = 0;
        $totalBeban = 0;
        $totalPajak = 0;
        $labaRugiKotor = 0;
        $labaRugiSebelumPajak = 0;
        $labaRugiBersih = 0;
    @endphp
    <thead>
        <tr>
            <th colspan="2">Penjualan</th>
        </tr>
    </thead>
    {{-- penjualan --}}
    <tbody>
        @foreach ($rekeningPenjualan as $item)
            @php
                $mutasiDebet = 0;
                $mutasiKredit = 0;
                // cek transaksi di field kode
                $cekTransaksiDiKode = \DB::table('view_laba_rugi')
                    ->where('bulan', $month)
                    ->where('tahun', $year)
                    ->where('kode', $item->kode_akun)
                    ->count();
                
                if ($cekTransaksiDiKode > 0) {
                    $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Debit')
                        ->sum('view_laba_rugi.nominal');
                
                    $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Kredit')
                        ->sum('view_laba_rugi.nominal');
                
                    $mutasiDebet += $sumMutasiDebetDiKode;
                    $mutasiKredit += $sumMutasiKreditDiKode;
                
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                } else {
                    // cek transaksi di field lawan
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }
                $penjualan = $mutasiKredit - $mutasiDebet;
                $totalPenjualan += $penjualan;
            @endphp
            <tr>
                <td>
                    {{ $item->nama }}
                </td>
                <td>
                    {{ $penjualan }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <thead>
        <tr>
            <th>Total Penjualan</th>
            <th>{{ $totalPenjualan }}</th>
        </tr>
    </thead>
    {{-- Harga Pokok Penjualan --}}
    {{-- <thead >
            <tr>
              <th>Harga Pokok Penjualan</th>
              <th>({{$hpp}})</th>
            </tr>
          </thead> --}}
    {{-- laba rugi kotor --}}
    @php
        $labaRugiKotor = $totalPenjualan;
    @endphp
    <thead>
        <tr>
            <th>Laba Rugi Kotor</th>
            <th>{{ $labaRugiKotor }}</th>
        </tr>
    </thead>
    {{-- beban --}}
    <thead>
        <tr>
            <th colspan="2">Beban</th>
        </tr>
    </thead>
    {{-- all beban --}}
    <tbody>
        @foreach ($rekeningBeban as $item)
            @php
                $mutasiDebet = 0;
                $mutasiKredit = 0;
                // cek transaksi di field kode
                $cekTransaksiDiKode = \DB::table('view_laba_rugi')
                    ->where('bulan', $month)
                    ->where('tahun', $year)
                    ->where('kode', $item->kode_akun)
                    ->count();
                
                if ($cekTransaksiDiKode > 0) {
                    $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Debit')
                        ->sum('view_laba_rugi.nominal');
                
                    $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Kredit')
                        ->sum('view_laba_rugi.nominal');
                
                    $mutasiDebet += $sumMutasiDebetDiKode;
                    $mutasiKredit += $sumMutasiKreditDiKode;
                
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                } else {
                    // cek transaksi di field lawan
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }
                if ($item->tipe == 'Debit') {
                    $beban = $mutasiDebet - $mutasiKredit;
                    $totalBeban += $beban;
                } else {
                    $beban = $mutasiKredit - $mutasiDebet;
                    $totalBeban -= $beban;
                }
            @endphp
            <tr>
                <td>
                    {{ $item->nama }}
                </td>
                <td>
                    {{ $item->tipe == 'Debit' ? $beban : '(' . $beban . ')' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <thead>
        <tr>
            <th>Total Beban</th>
            <th>({{ $totalBeban }})</th>
        </tr>
    </thead>
    {{-- laba rugi sebelum pajak --}}
    @php
        $labaRugiSebelumPajak = $labaRugiKotor - $totalBeban;
    @endphp
    <thead>
        <tr>
            <th>Laba Rugi Sebelum Pajak</th>
            <th>{{ $labaRugiSebelumPajak }}</th>
        </tr>
    </thead>
    {{-- pajak --}}
    <thead>
        <tr>
            <th colspan="2">Pajak</th>
        </tr>
    </thead>
    {{-- all pajak --}}
    <tbody>
        @foreach ($rekeningPajak as $item)
            @php
                $mutasiDebet = 0;
                $mutasiKredit = 0;
                // cek transaksi di field kode
                $cekTransaksiDiKode = \DB::table('view_laba_rugi')
                    ->where('bulan', $month)
                    ->where('tahun', $year)
                    ->where('kode', $item->kode_akun)
                    ->count();
                
                if ($cekTransaksiDiKode > 0) {
                    $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Debit')
                        ->sum('view_laba_rugi.nominal');
                
                    $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')
                        ->where('kode', $item->kode_akun)
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('tipe', 'Kredit')
                        ->sum('view_laba_rugi.nominal');
                
                    $mutasiDebet += $sumMutasiDebetDiKode;
                    $mutasiKredit += $sumMutasiKreditDiKode;
                
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                } else {
                    // cek transaksi di field lawan
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \DB::table('view_laba_rugi')
                        ->where('bulan', $month)
                        ->where('tahun', $year)
                        ->where('lawan', $item->kode_akun)
                        ->count();
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Kredit')
                            ->sum('view_laba_rugi.nominal');
                
                        $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')
                            ->where('lawan', $item->kode_akun)
                            ->where('bulan', $month)
                            ->where('tahun', $year)
                            ->where('tipe', 'Debit')
                            ->sum('view_laba_rugi.nominal');
                
                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }
                if ($item->tipe == 'Debit') {
                    $pajak = $mutasiDebet - $mutasiKredit;
                    $totalPajak += $pajak;
                } else {
                    $pajak = $mutasiKredit - $mutasiDebet;
                    $totalPajak -= $pajak;
                }
            @endphp
            <tr>
                <td>
                    {{ $item->nama }}
                </td>
                <td>
                    {{ $item->tipe == 'Debit' ? $pajak : '(' . $pajak . ')' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <thead>
        <tr>
            <th>Total Pajak</th>
            <th>({{ $totalPajak }})</th>
        </tr>
    </thead>
    {{-- laba rugi setelah pajak / bersih --}}
    @php
        $labaRugiBersih = $labaRugiSebelumPajak - $totalPajak;
        
        // // cek is data available
        // $isAvailable = \DB::table('support_ekuitas')
        //     ->where('bulan', $month)
        //     ->where('tahun', $year)
        //     ->count();
        // if ($isAvailable == 0) {
        //     // insert ke table support ekuitas
        //     \DB::table('support_ekuitas')->insert([
        //         'bulan' => $month,
        //         'tahun' => $year,
        //         'laba_rugi_bersih' => $labaRugiBersih,
        //     ]);
        // } else {
        //     \DB::table('support_ekuitas')
        //         ->where('bulan', $month)
        //         ->where('tahun', $year)
        //         ->update([
        //             'bulan' => $month,
        //             'tahun' => $year,
        //             'laba_rugi_bersih' => $labaRugiBersih,
        //         ]);
        // }
        
    @endphp
    <thead>
        <tr>
            <th>Laba Rugi Bersih</th>
            <th>{{ $labaRugiBersih }}</th>
        </tr>
    </thead>
</table>
@php
$name = 'Laba Rugi.xls';
header('Content-Type: application/xls');
header("Content-Disposition: attachment; filename=$name");
@endphp
