<div class="card-body">
    <form action="{{ url('general-ledger/neraca/export') }}" method="POST" id="form-download">
        @csrf
        <input type="hidden" name="sampai" value="{{$sampai}}">
        <div class="col-12 my-3 text-right pull-right">
            <button type="submit" class="btn btn-success"> <i class="fas fa-file-excel"></i> Download Excel</button>
        </div>
    </form>

    <h5 class="text-center"><b>Laporan Posisi Keuangan (Neraca)</b></h5>
    <h6 class="text-center">Per {{date('d-m-Y', strtotime($sampai))}}</h6>

    @if ($neraca['seimbang'])
        <p class="text-center"><span class="badge badge-success">Seimbang</span></p>
    @else
        <p class="text-center"><span class="badge badge-danger">Tidak Seimbang — selisih Rp. {{number_format($neraca['selisih'], 2, ',', '.')}}</span></p>
    @endif

    <table class="table table-bordered mt-3">
        <thead class="bg-primary text-white">
            <tr><th colspan="2">ASET</th></tr>
        </thead>
        <tbody>
            @foreach ($neraca['aset'] as $row)
            <tr>
                <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                <td class="text-right">{{number_format($row['nilai'], 2, ',', '.')}}</td>
            </tr>
            @endforeach
            <tr>
                <td><b>Total Aset</b></td>
                <td class="text-right"><b>{{number_format($neraca['totalAset'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered mt-3">
        <thead class="bg-primary text-white">
            <tr><th colspan="2">LIABILITAS</th></tr>
        </thead>
        <tbody>
            @foreach ($neraca['liabilitas'] as $row)
            <tr>
                <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                <td class="text-right">{{number_format($row['nilai'], 2, ',', '.')}}</td>
            </tr>
            @endforeach
            <tr>
                <td><b>Total Liabilitas</b></td>
                <td class="text-right"><b>{{number_format($neraca['totalLiabilitas'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered mt-3">
        <thead class="bg-primary text-white">
            <tr><th colspan="2">EKUITAS</th></tr>
        </thead>
        <tbody>
            @foreach ($neraca['ekuitas'] as $row)
            <tr>
                <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                <td class="text-right">{{number_format($row['nilai'], 2, ',', '.')}}</td>
            </tr>
            @endforeach
            <tr>
                <td>Laba (Rugi) Tahun Berjalan</td>
                <td class="text-right">{{number_format($neraca['labaBerjalan'], 2, ',', '.')}}</td>
            </tr>
            <tr>
                <td><b>Total Ekuitas</b></td>
                <td class="text-right"><b>{{number_format($neraca['totalEkuitas'] + $neraca['labaBerjalan'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered mt-3">
        <tbody>
            <tr>
                <td><b>Total Liabilitas &amp; Ekuitas</b></td>
                <td class="text-right"><b>{{number_format($neraca['totalPasiva'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>
</div>
