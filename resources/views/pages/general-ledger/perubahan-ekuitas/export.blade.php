<table border="1">
    <thead>
        <tr>
            <th colspan="4" style="text-align: center;"><b>Laporan Perubahan Ekuitas</b></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;"><b>Periode {{date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))}}</b></th>
        </tr>
        <tr>
            <th>Akun Ekuitas</th>
            <th>Saldo Awal</th>
            <th>Perubahan Periode Ini</th>
            <th>Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($perubahan['baris'] as $row)
        <tr>
            <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
            <td>{{number_format($row['awal'], 2, ',', '.')}}</td>
            <td>{{number_format($row['perubahan'], 2, ',', '.')}}</td>
            <td>{{number_format($row['akhir'], 2, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr>
            <td><b>Subtotal Akun Ekuitas</b></td>
            <td><b>{{number_format($perubahan['totalAwal'], 2, ',', '.')}}</b></td>
            <td><b>{{number_format($perubahan['totalPerubahan'], 2, ',', '.')}}</b></td>
            <td><b>{{number_format($perubahan['totalAkhir'], 2, ',', '.')}}</b></td>
        </tr>
        <tr>
            <td>Akumulasi Laba (Rugi) Periode Lalu</td>
            <td>{{number_format($perubahan['labaLalu'], 2, ',', '.')}}</td>
            <td>-</td>
            <td>{{number_format($perubahan['labaLalu'], 2, ',', '.')}}</td>
        </tr>
        <tr>
            <td>Laba (Rugi) Periode Berjalan</td>
            <td>-</td>
            <td>{{number_format($perubahan['labaPeriode'], 2, ',', '.')}}</td>
            <td>{{number_format($perubahan['labaPeriode'], 2, ',', '.')}}</td>
        </tr>
        <tr>
            <td><b>Total Ekuitas Akhir</b></td>
            <td colspan="2"></td>
            <td><b>{{number_format($perubahan['totalEkuitasAkhir'], 2, ',', '.')}}</b></td>
        </tr>
    </tbody>
</table>
