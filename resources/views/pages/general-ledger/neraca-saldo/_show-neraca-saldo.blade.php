<div class="card-block table-border-style">
    {{-- <br> --}}
    <hr>
    <div class="row d-flex mb-3 justify-content-between">
        <div class="form-group ml-auto">
            <form target="_blank" action="{{ url('general-ledger/neraca-saldo/export') }}" method="post">
                @csrf
                <input type="hidden" name="kodeAkun" value="{{ $isAll ? 'all' : implode(',', $selectedAkun) }}">
                <input type="hidden" name="dari" value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
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
        <table class="table table-bordered">
            <thead class="bg-primary">
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
                        <td>{{ number_format($saldoAwal, 2, ',', '.') }}</td>
                        <td>-</td>
                    @else
                        <td>-</td>
                        <td>{{ number_format($saldoAwal * -1, 2, ',', '.') }}</td>
                    @endif
                    <td>{{ number_format($mutasiDebet, 2, ',', '.') }}</td>
                    <td>{{ number_format($mutasiKredit, 2, ',', '.') }}</td>
                    @if ($item->tipe == 'Debit')
                        <td>{{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                        <td>-</td>
                    @else
                        <td>-</td>
                        <td>{{ number_format($saldoAkhir * -1, 2, ',', '.') }}</td>
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
                    <th>{{ number_format($totalSaldoAwalDebet, 2, ',', '.') }}</th>
                    <th>{{ number_format($totalSaldoAwalKredit * -1, 2, ',', '.') }}</th>
                    <th>{{ number_format($totalMutasiDebet, 2, ',', '.') }}</th>
                    <th>{{ number_format($totalMutasiKredit, 2, ',', '.') }}</th>
                    <th>{{ number_format($totalSaldoAkhirDebet, 2, ',', '.') }}</th>
                    <th>{{ number_format($totalSaldoAkhirKredit * -1, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
