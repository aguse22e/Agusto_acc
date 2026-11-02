<table border="1">
    <thead class="bg-primary">
        <tr>
            <th colspan="8" style="text-align: center;"><b>Neraca Saldo</b></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;"><b>Periode {{date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))}}</b></th>
        </tr>
        <tr>
            <th rowspan="2" style="vertical-align:middle">Kode Akun</th>
            <th rowspan="2" style="vertical-align:middle">Nama Akun</th>
            <th colspan="2" style="text-align: center;">Saldo Awal</th>
            <th colspan="2" style="text-align: center;">Mutasi</th>
            <th colspan="2" style="text-align: center;">Saldo Akhir</th>
        <tr>
            <th style="text-align: center;">Debet</th>
            <th style="text-align: center;">Kredit</th>
            <th style="text-align: center;">Debet</th>
            <th style="text-align: center;">Kredit</th>
            <th style="text-align: center;">Debet</th>
            <th style="text-align: center;">Kredit</th>
        </tr>
        </tr>
    </thead>
    <tbody>
        @php
            $totalSaldoAwalDebet = 0;
            $totalSaldoAwalKredit = 0;
            $totalMutasiDebet = 0;
            $totalMutasiKredit = 0;
            $totalSaldoAkhirDebet = 0;
            $totalSaldoAkhirKredit = 0;
        @endphp
        <?php
            foreach ($kodeAkun as $item) {

                $mutasiAwalDebet = 0;
                $mutasiAwalKredit = 0;
                
                $mutasiDebet = 0;
                $mutasiKredit = 0;

                // cek apakah ada jurnal awal di field kode
                $cekTransaksiAwalDiKode = \App\Models\Jurnal::where('tanggal', '<', $dari)->where('kode', $item->kode_akun)->count();

                if ($cekTransaksiAwalDiKode > 0) {
                    $sumMutasiAwalDebetDiKode = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('kode', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Debit')->sum('jurnal.nominal');
                    
                    $sumMutasiAwalKreditDiKode = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('kode', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Kredit')->sum('jurnal.nominal');

                    if ($item->tipe == 'Debit') {
                        $mutasiAwalDebet += $sumMutasiAwalDebetDiKode;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiKode;
                    }
                    else{
                        $mutasiAwalDebet += $sumMutasiAwalDebetDiKode;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiKode;
                    }

                    // cek apakah transaksi sebelumnya juga terdapat di field lawan
                    $cekTransaksiAwalDiLawan = \App\Models\Jurnal::where('tanggal', '<', $dari)->where('lawan', $item->kode_akun)->count();
                    if ($cekTransaksiAwalDiLawan > 0) {
                        $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                    
                        $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Debit')->sum('jurnal.nominal');

                        $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan;
                    }
                }
                else{ // cek apakah ada jurnal awal di field lawan
                    $cekTransaksiAwalDiLawan = \App\Models\Jurnal::where('tanggal', '<', $dari)->where('lawan', $item->kode_akun)->count();
                    if ($cekTransaksiAwalDiLawan > 0) {
                        $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                    
                        $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->where('tanggal', '<', $dari)->where('tipe', 'Debit')->sum('jurnal.nominal');

                        if ($item->tipe == 'Debit') {
                            $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan;

                            $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan;
                        }
                        else{
                            $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan;
                            $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan;
                        }
                    }
                    else{ //tidak ada jurnal awal di field kode maupun lawan
                        // if ($item->tipe == 'Debit') {
                        //     $mutasiAwalDebet += $item->saldo_awal;
                        // }
                        // else{
                        //     $mutasiAwalKredit += $item->saldo_awal;
                        // }
                    }
                }

                // cek transaksi di field kode
                $cekTransaksiDiKode = \App\Models\Jurnal::whereBetween('tanggal', [$dari, $sampai])->where('kode', $item->kode_akun)->count();
                
                if ($cekTransaksiDiKode > 0) {
                    $sumMutasiDebetDiKode = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('kode', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Debit')->sum('jurnal.nominal');
                    
                    $sumMutasiKreditDiKode = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('kode', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');

                    $mutasiDebet += $sumMutasiDebetDiKode;
                    $mutasiKredit += $sumMutasiKreditDiKode;

                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \App\Models\Jurnal::whereBetween('tanggal', [$dari, $sampai])->where('lawan', $item->kode_akun)->count();

                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');
                        
                        $sumMutasiKreditDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Debit')->sum('jurnal.nominal');

                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }
                else{ // cek transaksi di field lawan
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \App\Models\Jurnal::whereBetween('tanggal', [$dari, $sampai])->where('lawan', $item->kode_akun)->count();
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');
                        
                        $sumMutasiKreditDiLawan = \DB::table('jurnal')->whereNull('jurnal.deleted_at')->where('lawan', $item->kode_akun)->whereBetween('tanggal', [$dari, $sampai])->where('tipe', 'Debit')->sum('jurnal.nominal');

                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }

                $saldoAwal = $mutasiAwalDebet - $mutasiAwalKredit;

                $saldoAkhir = ($mutasiAwalDebet + $mutasiDebet) - ($mutasiAwalKredit + $mutasiKredit);
                
                $totalMutasiDebet += $mutasiDebet;
                $totalMutasiKredit += $mutasiKredit;

                if ($item->tipe == 'Debit') {
                    $totalSaldoAwalDebet += $saldoAwal;
                    $totalSaldoAkhirDebet += $saldoAkhir;
                }
                else{
                    $totalSaldoAwalKredit += $saldoAwal;
                    $totalSaldoAkhirKredit += $saldoAkhir;
                }
        ?>
            <tr>
                <td>{{ $item->kode_akun }}</td>
                <td>{{ $item->nama }}</td>
                @if ($item->tipe == 'Debit')
                    <td>{{ $saldoAwal }}</td>
                    <td>-</td>
                @else
                    <td>-</td>
                    <td>{{ $saldoAwal * -1 }}</td>
                @endif
                <td>{{ $mutasiDebet }}</td>
                <td>{{ $mutasiKredit }}</td>
                @if ($item->tipe == 'Debit')
                    <td>{{ $saldoAkhir }}</td>
                    <td>-</td>
                @else
                    <td>-</td>
                    <td>{{ $saldoAkhir * -1 }}</td>
                @endif
            </tr>
        <?php
        // endforeach
            }
        ?>
    </tbody>
    <tfoot class="bg-primary">
        <tr>
            <th colspan="2" style="text-align: center">Total</th>
            <th>{{ $totalSaldoAwalDebet }}</th>
            <th>{{ $totalSaldoAwalKredit * -1 }}</th>
            <th>{{ $totalMutasiDebet }}</th>
            <th>{{ $totalMutasiKredit }}</th>
            <th>{{ $totalSaldoAkhirDebet }}</th>
            <th>{{ $totalSaldoAkhirKredit * -1 }}</th>
        </tr>
    </tfoot>
</table>
@php
    $name = 'Neraca Saldo.xls';
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$name");
@endphp
