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
            <h5>Tutup Buku (Jurnal Penutup)</h5>
        </div>
        <div class="card-body">
            <p>Menolkan saldo akun nominal (Pendapatan 4xxx, Beban 5xxx, Pajak 6xxx) ke akun Laba Ditahan melalui bukti Memorial penutup. Pilih tanggal tutup dan akun laba ditahan, lalu periksa pratinjau sebelum memproses.</p>
            <form action="{{ url('general-ledger/tutup-buku') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <label for="">Tanggal Tutup</label>
                        <input type="date" name="tanggal" autocomplete="off" class="form-control form-control-lg" value="{{isset($tanggal) ? $tanggal : date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Akun Laba Ditahan (Ekuitas)</label>
                        <select name="akunLabaDitahan" class="form-control form-control-lg" required>
                            <option value="">--Pilih Akun Ekuitas--</option>
                            @foreach ($akunEkuitas as $item)
                                <option value="{{$item->kode_akun}}" {{isset($akunLabaDitahan) && $akunLabaDitahan->kode_akun == $item->kode_akun ? 'selected' : ''}}>{{$item->kode_akun . ' ~ ' . $item->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary"> <i class="fas fa-search"></i> Pratinjau</button>
                    </div>
                </div>
            </form>
        </div>

        @if (isset($penutup) && isset($tanggal) && isset($akunLabaDitahan))
        <div class="card-body">
            <h6>Pratinjau Jurnal Penutup per {{date('d-m-Y', strtotime($tanggal))}} ke {{$akunLabaDitahan->kode_akun . ' ~ ' . $akunLabaDitahan->nama}}</h6>
            <table class="table table-bordered mt-3">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Akun Nominal</th>
                        <th class="text-right">Saldo</th>
                        <th>Voucher Penutup</th>
                        <th class="text-right">Jumlah Ditutup</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penutup['debit'] as $row)
                    <tr>
                        <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                        <td class="text-right">{{number_format($row['saldo'], 2, ',', '.')}}</td>
                        <td>BMM (Debit)</td>
                        <td class="text-right">{{number_format($row['jumlah'], 2, ',', '.')}}</td>
                    </tr>
                    @endforeach
                    @foreach ($penutup['kredit'] as $row)
                    <tr>
                        <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                        <td class="text-right">{{number_format($row['saldo'], 2, ',', '.')}}</td>
                        <td>BMK (Kredit)</td>
                        <td class="text-right">{{number_format($row['jumlah'], 2, ',', '.')}}</td>
                    </tr>
                    @endforeach
                    @if (count($penutup['debit']) == 0 && count($penutup['kredit']) == 0)
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada saldo akun nominal yang perlu ditutup sampai tanggal ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            @if (count($penutup['debit']) > 0 || count($penutup['kredit']) > 0)
            <form action="{{ url('general-ledger/tutup-buku/proses') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{$tanggal}}">
                <input type="hidden" name="akunLabaDitahan" value="{{$akunLabaDitahan->kode_akun}}">
                <p class="text-danger">Proses ini membuat bukti Memorial penutup dan tidak dapat dibatalkan dari halaman ini (pembatalan dilakukan dengan menghapus memorial penutup). Pastikan tanggal dan akun sudah benar.</p>
                <button type="submit" class="btn btn-warning" onclick="return confirm('Proses tutup buku sampai tanggal ini?')"> <i class="fas fa-book"></i> Proses Tutup Buku</button>
            </form>
            @endif
        </div>
        @endif
    </div>
@endsection
