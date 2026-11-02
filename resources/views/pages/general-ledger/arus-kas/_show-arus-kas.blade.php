<div class="card-body">
    <form action="{{ url('general-ledger/arus-kas/export') }}" method="POST" id="form-download">
        @csrf
        <input type="hidden" name="dari" value="{{$dari}}">
        <input type="hidden" name="sampai" value="{{$sampai}}">
        <div class="col-12 my-3 text-right pull-right">
            <button type="submit" class="btn btn-success"> <i class="fas fa-file-excel"></i> Download Excel</button>
        </div>
    </form>

    <h5 class="text-center"><b>Laporan Arus Kas</b></h5>
    <h6 class="text-center">Periode {{date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))}}</h6>

    @foreach (['operasi' => 'Aktivitas Operasi', 'investasi' => 'Aktivitas Investasi', 'pendanaan' => 'Aktivitas Pendanaan'] as $kunci => $judul)
    <table class="table table-bordered mt-3">
        <thead class="bg-primary text-white">
            <tr><th colspan="2">{{$judul}}</th></tr>
        </thead>
        <tbody>
            @foreach ($arusKas[$kunci] as $row)
            <tr>
                <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                <td class="text-right">{{number_format($row['nilai'], 2, ',', '.')}}</td>
            </tr>
            @endforeach
            <tr>
                <td><b>Kas Bersih dari {{$judul}}</b></td>
                <td class="text-right"><b>{{number_format($arusKas['total' . ucfirst($kunci)], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <table class="table table-bordered mt-3">
        <tbody>
            <tr>
                <td><b>Kenaikan (Penurunan) Bersih Kas</b></td>
                <td class="text-right"><b>{{number_format($arusKas['kenaikanBersih'], 2, ',', '.')}}</b></td>
            </tr>
            <tr>
                <td>Kas &amp; Setara Kas Awal Periode</td>
                <td class="text-right">{{number_format($arusKas['kasAwal'], 2, ',', '.')}}</td>
            </tr>
            <tr>
                <td><b>Kas &amp; Setara Kas Akhir Periode</b></td>
                <td class="text-right"><b>{{number_format($arusKas['kasAkhir'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>

    @if ($arusKas['cocok'])
        <p class="text-center"><span class="badge badge-success">Rekonsiliasi cocok dengan saldo kas di Neraca</span></p>
    @else
        <p class="text-center"><span class="badge badge-danger">Rekonsiliasi tidak cocok — periksa jurnal periode ini</span></p>
    @endif
</div>
