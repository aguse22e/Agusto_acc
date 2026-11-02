<div class="card-block table-border-style">
    {{-- <br> --}}
    <hr>
    <div class="row d-flex mb-3 justify-content-between">
        <div class="form-group ml-auto">
            <form target="_blank" action="{{ url('general-ledger/laba-rugi/export') }}" method="post">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="btn btn-success btn-sm mr-3"><i class="fa fa-file-excel"></i> Export</button>
            </form>
            {{-- <a href="{{ url('general-ledger/neraca-saldo/print') ."?dari=$dari&sampai=$_GET[sampai]&kodeAkunDari=$_GET[kodeAkunDari]&kodeAkunSampai=$_GET[kodeAkunSampai]" }}"
                class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-print" aria-hidden="true"></i> Cetak
            </a>
            <a href="{{ url('general-ledger/neraca-saldo/print') ."?dari=$dari&sampai=$_GET[sampai]&kodeAkunDari=$_GET[kodeAkunDari]&kodeAkunSampai=$_GET[kodeAkunSampai]&xls=true" }}"
                class="btn btn-success btn-sm" target="_blank">
                <i class="fa fa-download"></i> Download xls
            </a> --}}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-custom">
            @php
                $totalPenjualan = 0;
                $totalBeban = 0;
                $totalPajak = 0;
                $labaRugiKotor = 0;
                $labaRugiSebelumPajak = 0;
                $labaRugiBersih = 0;
            @endphp
            <thead class="bg-primary">
                <tr>
                    <th colspan="2">Pendapatan</th>
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
                            {{ number_format($penjualan, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <thead class="bg-primary">
                <tr>
                    <th>Total Pendapatan</th>
                    <th>{{ number_format($totalPenjualan, 2, ',', '.') }}</th>
                </tr>
            </thead>
            {{-- Harga Pokok Penjualan --}}
            {{-- <thead  class="bg-primary">
            <tr>
              <th>Harga Pokok Penjualan</th>
              <th>({{number_format($hpp, 2, ',', '.')}})</th>
            </tr>
          </thead> --}}
            {{-- laba rugi kotor --}}
            @php
                $labaRugiKotor = $totalPenjualan;
            @endphp
            <thead class="bg-primary">
                <tr>
                    <th>Laba Rugi Kotor</th>
                    <th>{{ number_format($labaRugiKotor, 2, ',', '.') }}</th>
                </tr>
            </thead>
            {{-- beban --}}
            <thead  class="bg-primary">
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
                            {{ $item->tipe == 'Debit' ? number_format($beban, 2, ',', '.') : '(' . number_format($beban, 2, ',', '.') . ')' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <thead  class="bg-primary">
                <tr>
                    <th>Total Beban</th>
                    <th>({{ number_format($totalBeban, 2, ',', '.') }})</th>
                </tr>
            </thead>
            {{-- laba rugi sebelum pajak --}}
            @php
                $labaRugiSebelumPajak = $labaRugiKotor - $totalBeban;
            @endphp
            <thead  class="bg-primary">
                <tr>
                    <th>Laba Rugi Sebelum Pajak</th>
                    <th>{{ number_format($labaRugiSebelumPajak, 2, ',', '.') }}</th>
                </tr>
            </thead>
            {{-- pajak --}}
            <thead  class="bg-primary">
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
                            {{ $item->tipe == 'Debit' ? number_format($pajak, 2, ',', '.') : '(' . number_format($pajak, 2, ',', '.') . ')' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <thead  class="bg-primary">
                <tr>
                    <th>Total Pajak</th>
                    <th>({{ number_format($totalPajak, 2, ',', '.') }})</th>
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
            <thead  class="bg-primary">
                <tr>
                    <th>Laba Rugi Bersih</th>
                    <th>{{ number_format($labaRugiBersih, 2, ',', '.') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
