<div class="card-body">
    <form action="{{ url('general-ledger/perubahan-ekuitas/export') }}" method="POST" id="form-download">
        @csrf
        <input type="hidden" name="dari" value="{{$dari}}">
        <input type="hidden" name="sampai" value="{{$sampai}}">
        <div class="col-12 my-3 text-right pull-right">
            <button type="submit" class="btn btn-success"> <i class="fas fa-file-excel"></i> Download Excel</button>
        </div>
    </form>

    <h5 class="text-center"><b>Laporan Perubahan Ekuitas</b></h5>
    <h6 class="text-center">Periode {{date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))}}</h6>

    <table class="table table-bordered mt-3">
        <thead class="bg-primary text-white">
            <tr>
                <th>Akun Ekuitas</th>
                <th class="text-right">Saldo Awal</th>
                <th class="text-right">Perubahan Periode Ini</th>
                <th class="text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perubahan['baris'] as $row)
            <tr>
                <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
                <td class="text-right">{{number_format($row['awal'], 2, ',', '.')}}</td>
                <td class="text-right">{{number_format($row['perubahan'], 2, ',', '.')}}</td>
                <td class="text-right">{{number_format($row['akhir'], 2, ',', '.')}}</td>
            </tr>
            @endforeach
            <tr>
                <td><b>Subtotal Akun Ekuitas</b></td>
                <td class="text-right"><b>{{number_format($perubahan['totalAwal'], 2, ',', '.')}}</b></td>
                <td class="text-right"><b>{{number_format($perubahan['totalPerubahan'], 2, ',', '.')}}</b></td>
                <td class="text-right"><b>{{number_format($perubahan['totalAkhir'], 2, ',', '.')}}</b></td>
            </tr>
            <tr>
                <td>Akumulasi Laba (Rugi) Periode Lalu</td>
                <td class="text-right">{{number_format($perubahan['labaLalu'], 2, ',', '.')}}</td>
                <td class="text-right">-</td>
                <td class="text-right">{{number_format($perubahan['labaLalu'], 2, ',', '.')}}</td>
            </tr>
            <tr>
                <td>Laba (Rugi) Periode Berjalan</td>
                <td class="text-right">-</td>
                <td class="text-right">{{number_format($perubahan['labaPeriode'], 2, ',', '.')}}</td>
                <td class="text-right">{{number_format($perubahan['labaPeriode'], 2, ',', '.')}}</td>
            </tr>
            <tr>
                <td><b>Total Ekuitas Akhir</b></td>
                <td class="text-right" colspan="2"></td>
                <td class="text-right"><b>{{number_format($perubahan['totalEkuitasAkhir'], 2, ',', '.')}}</b></td>
            </tr>
        </tbody>
    </table>
    <p class="text-muted">Total Ekuitas Akhir harus sama dengan total Ekuitas + Laba Tahun Berjalan pada Laporan Posisi Keuangan (Neraca) per tanggal sampai.</p>
</div>
