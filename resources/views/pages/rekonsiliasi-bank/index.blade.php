@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => $pageTitle,
    'pageSubtitle' => '',
    'pageIcon' => $pageIcon,
    'parentMenu' => $parentMenu,
    'current' => $current
    ])
@endsection

@section('content')
    @include('components.notification')

    <div class="card">
        <div class="card-header">
            <h5>Rekonsiliasi Bank</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('rekonsiliasi-bank.index') }}">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Akun Bank</label>
                    <div class="col-sm-4">
                        <select name="akun" class="form-control" required>
                            <option value="">Pilih akun bank</option>
                            @foreach ($kodeAkun as $akun)
                                <option value="{{ $akun->kode_akun }}" {{ ($akunTerpilih ?? '') == $akun->kode_akun ? 'selected' : '' }}>{{ $akun->kode_akun }} - {{ $akun->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Periode</label>
                    <div class="col-sm-2">
                        <input type="month" name="periode" class="form-control" value="{{ $periode ?? '' }}" required>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="feather icon-search"></i> Tampilkan</button>
                    </div>
                </div>
            </form>

            @if (!empty($hasil))
                <hr>
                <form method="POST" action="{{ route('rekonsiliasi-bank.saldo') }}">
                    @csrf
                    <input type="hidden" name="akun" value="{{ $akunTerpilih }}">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Saldo menurut rekening koran per {{ date('d-m-Y', strtotime($hasil['akhirPeriode'])) }}</label>
                        <div class="col-sm-3">
                            <input type="number" step="0.01" name="saldo_koran" class="form-control" value="{{ $hasil['saldoKoran'] }}" required>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="feather icon-save"></i> Simpan Saldo Koran</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Saldo menurut rekening koran</td>
                            <td class="text-right">{{ number_format($hasil['saldoKoran'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Ditambah setoran dalam perjalanan (transaksi Masuk yang belum kliring)</td>
                            <td class="text-right">{{ number_format($hasil['belumMasuk'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Dikurangi cek atau giro beredar (transaksi Keluar yang belum kliring)</td>
                            <td class="text-right">{{ number_format($hasil['belumKeluar'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><b>Saldo koran yang disesuaikan</b></td>
                            <td class="text-right"><b>{{ number_format($hasil['saldoDisesuaikan'], 2, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td><b>Saldo menurut pembukuan (jurnal) sampai akhir periode</b></td>
                            <td class="text-right"><b>{{ number_format($hasil['saldoBuku'], 2, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td><b>Selisih</b></td>
                            <td class="text-right">
                                @if (round($hasil['selisih'], 2) == 0)
                                    <span class="badge badge-success">Seimbang (0)</span>
                                @else
                                    <span class="badge badge-danger">{{ number_format($hasil['selisih'], 2, ',', '.') }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-muted">Selisih selain nol berarti ada mutasi pada rekening koran yang belum dicatat di pembukuan, atau sebaliknya. Mutasi bank yang dicatat lewat Transaksi Kas atau Memorial ikut terhitung pada saldo pembukuan, tetapi status kliringnya tidak tampil pada daftar di bawah karena daftar ini hanya memuat Transaksi Bank.</p>

                <hr>
                <h6>Status Kliring Transaksi Bank</h6>
                <form method="POST" action="{{ route('rekonsiliasi-bank.kliring') }}">
                    @csrf
                    <input type="hidden" name="akun" value="{{ $akunTerpilih }}">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kliring</th>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Tipe</th>
                                <th>No Referensi</th>
                                <th class="text-right">Total</th>
                                <th>Status</th>
                                <th>Tanggal Kliring</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil['transaksi'] as $trx)
                                <tr>
                                    <td>
                                        <input type="hidden" name="semua[]" value="{{ $trx->kode_transaksi_bank }}">
                                        <input type="checkbox" name="kliring[]" value="{{ $trx->kode_transaksi_bank }}" {{ $trx->status_kliring == 'Kliring' ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ date('d-m-Y', strtotime($trx->tanggal)) }}</td>
                                    <td>{{ $trx->kode_transaksi_bank }}</td>
                                    <td>{{ $trx->tipe }}</td>
                                    <td>{{ $trx->no_referensi ?? '' }}</td>
                                    <td class="text-right">{{ number_format($trx->total, 2, ',', '.') }}</td>
                                    <td>{{ $trx->status_kliring }}</td>
                                    <td>{{ $trx->tanggal_kliring ? date('d-m-Y', strtotime($trx->tanggal_kliring)) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="feather icon-save"></i> Simpan Status Kliring</button>
                </form>
            @endif
        </div>
    </div>
@endsection
