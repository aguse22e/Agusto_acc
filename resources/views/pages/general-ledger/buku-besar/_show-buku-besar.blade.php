<div class="card-block table-border-style">
    {{-- <br> --}}
    <hr>
    <div class="row d-flex mb-3 justify-content-between">
        <div class="form-group ml-auto">
            <form target="_blank" action="{{ url('general-ledger/buku-besar/export') }}" method="post">
                @csrf
                <input type="hidden" name="kodeAkun" value="{{$isAll ? 'all' : implode(',', $selectedAkun)}}">
                <input type="hidden" name="dari" value="{{$dari}}">
                <input type="hidden" name="sampai" value="{{$sampai}}">
                <button type="submit" class="btn btn-success btn-sm mr-3"><i class="fa fa-file-excel"></i> Export</button>
            </form>
            {{-- <a href="{{ url('general-ledger/buku-besar/print') ."?dari=$dari&sampai=$_GET[sampai]&kodeRekeningDari=$_GET[kodeRekeningDari]&kodeRekeningSampai=$_GET[kodeRekeningSampai]" }}"
                class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-print" aria-hidden="true"></i> Cetak
            </a>
            <a href="{{ url('general-ledger/buku-besar/print') ."?dari=$dari&sampai=$_GET[sampai]&kodeRekeningDari=$_GET[kodeRekeningDari]&kodeRekeningSampai=$_GET[kodeRekeningSampai]&xls=true" }}"
                class="btn btn-success btn-sm" target="_blank">
                <i class="fa fa-download"></i> Download xls
            </a> --}}
        </div>
    </div>

    @foreach ($kodeAkun as $item)
        @php
            $totalDebit = 0;
            $totalKredit = 0;
            $saldoAkhir = 0;
            $saldoAwalDebit2 = 0;
            $saldoAwalKredit2 = 0;
            $saldoAwalDebit = 0;
            $saldoAwalKredit = 0;
        @endphp
        {{-- <center>
            <h6><b>Buku Besar : {{ $item->nama . ' - ' . $item->kode_akun }} </b></h6>
        </center> --}}
        <div class="table-responsive">
            <table class="table table-custom">
                <thead class="bg-primary">
                    <tr>
                        <th colspan="7" class="text-center">Buku Besar : {{ $item->nama . ' - ' . $item->kode_akun}}</th>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Keterangan</th>
                        <th>Lawan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // count jumlah transaksi masing2 kode rekening sebelum tanggal dari
                        $cekTransaksi = \App\Models\Jurnal::where('tanggal', '<', $dari)
                            ->where('kode', $item->kode_akun)
                            ->orWhere('lawan', $item->kode_akun)
                            ->count();

                        // cek apakah ada transaksi sebelum tanggal dari
                        // untuk ngambil saldo awal sebelum tanggal dari
                        if ($cekTransaksi > 0) {
                            // cek apakah rekening terdapat di field kode di table jurnal
                            $isFieldKode = \App\Models\Jurnal::where('kode', $item->kode_akun)
                                ->where('tanggal', '<', $dari)
                                ->count();

                            if ($isFieldKode > 0) {
                                $saldoAwalDebit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                    ->where('kode', $item->kode_akun)
                                    ->where('tipe', 'Debit')
                                    ->where('tanggal', '<', $dari)
                                    ->get()[0]->nominal;

                                $saldoAwalKredit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                    ->where('kode', $item->kode_akun)
                                    ->where('tipe', 'Kredit')
                                    ->where('tanggal', '<', $dari)
                                    ->get()[0]->nominal;

                                // cek apakah rekening juga terdapat di field lawan di table jurnal
                                $isFieldLawan = \App\Models\Jurnal::where('lawan', $item->kode_akun)
                                    ->where('tanggal', '<', $dari)
                                    ->count();
                                if ($isFieldLawan > 0) {
                                    $saldoAwalDebit2 = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                        ->where('lawan', $item->kode_akun)
                                        ->where('tipe', 'Kredit')
                                        ->where('tanggal', '<', $dari)
                                        ->get()[0]->nominal;

                                    $saldoAwalKredit2 = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                        ->where('lawan', $item->kode_akun)
                                        ->where('tipe', 'Debit')
                                        ->where('tanggal', '<', $dari)
                                        ->get()[0]->nominal;
                                }
                            } else {
                                //rekening tsb tidak terdapat di field kode dan hanya terdapat di field lawan
                                $saldoAwalDebit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                    ->where('lawan', $item->kode_akun)
                                    ->where('tipe', 'Kredit')
                                    ->where('tanggal', '<', $dari)
                                    ->get()[0]->nominal;

                                $saldoAwalKredit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))
                                    ->where('lawan', $item->kode_akun)
                                    ->where('tipe', 'Debit')
                                    ->where('tanggal', '<', $dari)
                                    ->get()[0]->nominal;
                            }

                            // hitung saldoAwal dari rekening
                            if ($item->tipe == 'Debit') {
                                $saldoAkhir = ($saldoAwalDebit + $saldoAwalDebit2) - ($saldoAwalKredit + $saldoAwalKredit2);
                            } else {
                                // Akun saldo normal Kredit: saldo awal = total sisi Kredit - total sisi Debit
                                $saldoAkhir = ($saldoAwalKredit + $saldoAwalKredit2) - ($saldoAwalDebit + $saldoAwalDebit2);
                            }
                        }
                        // tidak ada transaksi untuk rekening tsb sebelum tanggal dari
                        else {
                            // set saldo akhir = saldo awal rekening
                            $saldoAkhir = 0;
                        }
                        // echo $saldoAwalDebit . "saldoawaldebet<br>";
                        // echo $saldoAwalDebit2 . "saldoawaldebet2<br>";
                        // echo $saldoAwalKredit . "saldoawalkredit<br>";
                        // echo $saldoAwalKredit2 . "saldoawalkredit2<br>";
                    @endphp
                    {{-- print saldo awal --}}
                    <tr>
                        <td>{{ date('d-m-Y', strtotime($dari)) }}</td>
                        <td>-</td>
                        <td>Saldo Awal</td>
                        <td colspan="3"></td>
                        <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                    </tr>
                    @php
                        $getBukuBesar = \App\Models\Jurnal::select('id', 'tanggal', 'kode_transaksi', 'keterangan', 'kode', 'lawan', 'nominal', 'tipe')
                            ->whereBetween('tanggal', [$dari, $sampai])
                            ->where('kode', $item->kode_akun)
                            ->orWhere('lawan', $item->kode_akun)
                            ->whereBetween('tanggal', [$dari, $sampai])
                            ->orderBy('tanggal', 'ASC')
                            ->get();
                    @endphp
                    @foreach ($getBukuBesar as $val)
                        {{-- cek posisi lawan (pasangan) ada di field kode atau di field lawan --}}
                        @if ($val->kode == $item->kode_akun)
                            @php
                                $fieldLawan = 'lawan';
                            @endphp
                        @else
                            @php
                                $fieldLawan = 'kode';
                            @endphp
                        @endif
                        <tr>
                            <td>{{ date('d-m-Y', strtotime($val->tanggal)) }}</td>
                            <td>{{ $val->kode_transaksi }}</td>
                            <td>{{ $val->keterangan }}</td>
                            <td>{{ $val->$fieldLawan .' ~ ' .\App\Models\KodeAkun::select('nama')->where('kode_akun', $val->$fieldLawan)->get()[0]->nama }}
                            </td>
                            {{-- jika lawan terdapat di field lawan --}}
                            @if ($fieldLawan == 'lawan')
                                {{-- jika tipe transaksi = debet --}}
                                @if ($val->tipe == 'Debit')
                                    {{-- totaldebet bertambah --}}
                                    @php
                                        $totalDebit += $val->nominal;
                                    @endphp

                                    {{-- jika tipe rekening = debet --}}
                                    @if ($item->tipe == 'Debit')
                                        {{-- saldo akhir rekening bertambah --}}
                                        @php
                                            $saldoAkhir += $val->nominal;
                                        @endphp

                                        {{-- jika tipe rekening = kredit --}}
                                    @else
                                        {{-- saldo akhir rekening berkurang --}}
                                        @php
                                            $saldoAkhir -= $val->nominal;
                                        @endphp
                                    @endif

                                    <td>{{ number_format($val->nominal, 2, ',', '.') }}</td>
                                    <td>-</td>
                                    <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>

                                    {{-- jika tipe transaksi = kredit --}}
                                @else
                                    {{-- total kredit bertambah --}}
                                    @php
                                        $totalKredit += $val->nominal;
                                    @endphp

                                    {{-- jika tipe rekening = debet --}}
                                    @if ($item->tipe == 'Debit')
                                        {{-- saldo akhir berkurang --}}
                                        @php
                                            $saldoAkhir -= $val->nominal;
                                        @endphp

                                        {{-- jika tipe rekening = kredit --}}
                                    @else
                                        {{-- saldo akhir bertambah --}}
                                        @php
                                            $saldoAkhir += $val->nominal;
                                        @endphp
                                    @endif

                                    <td>-</td>
                                    <td>{{ number_format($val->nominal, 2, ',', '.') }}</td>
                                    <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                                @endif

                                {{-- jika lawan terdapat di field kode --}}
                            @else
                                {{-- jike tipe transaksi  = debet --}}
                                @if ($val->tipe == 'Debit')
                                    {{-- total kredit bertambah --}}
                                    @php
                                        $totalKredit += $val->nominal;
                                    @endphp

                                    {{-- jika tipe rekening = Debit --}}
                                    @if ($item->tipe == 'Debit')
                                        {{-- saldo akhir berkurang --}}
                                        @php
                                            $saldoAkhir -= $val->nominal;
                                        @endphp

                                        {{-- jika tipe rekening = kredit --}}
                                    @else
                                        @php
                                            $saldoAkhir += $val->nominal;
                                        @endphp
                                    @endif

                                    <td>-</td>
                                    <td>{{ number_format($val->nominal, 2, ',', '.') }}</td>
                                    <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>

                                    {{-- jika tipe transaksi = kredit --}}
                                @else
                                    {{-- total debet bertambah --}}
                                    @php
                                        $totalDebit += $val->nominal;
                                    @endphp

                                    {{-- jika tipe rekening = Debit --}}
                                    @if ($item->tipe == 'Debit')
                                        {{-- saldo akhir bertambah --}}
                                        @php
                                            $saldoAkhir += $val->nominal;
                                        @endphp

                                        {{-- jika tipe rekening = kredit --}}
                                    @else
                                        @php
                                            $saldoAkhir -= $val->nominal;
                                        @endphp
                                    @endif

                                    <td>{{ number_format($val->nominal, 2, ',', '.') }}</td>
                                    <td>-</td>
                                    <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                                @endif
                            @endif
                        </tr>
                    @endforeach
                    <tfoot class="bg-primary">
                        <tr>
                            <th colspan="4" style="text-align: center">Total</th>
                            <th>{{ number_format($totalDebit, 2, ',', '.') }}</th>
                            <th>{{ number_format($totalKredit, 2, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
        <hr>
    @endforeach
</div>
