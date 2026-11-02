<table border="1">
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Laporan Posisi Keuangan (Neraca)</b></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Per {{date('d-m-Y', strtotime($sampai))}}</b></th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="2"><b>ASET</b></td></tr>
        @foreach ($neraca['aset'] as $row)
        <tr>
            <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
            <td>{{number_format($row['nilai'], 2, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr>
            <td><b>Total Aset</b></td>
            <td><b>{{number_format($neraca['totalAset'], 2, ',', '.')}}</b></td>
        </tr>
        <tr><td colspan="2"><b>LIABILITAS</b></td></tr>
        @foreach ($neraca['liabilitas'] as $row)
        <tr>
            <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
            <td>{{number_format($row['nilai'], 2, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr>
            <td><b>Total Liabilitas</b></td>
            <td><b>{{number_format($neraca['totalLiabilitas'], 2, ',', '.')}}</b></td>
        </tr>
        <tr><td colspan="2"><b>EKUITAS</b></td></tr>
        @foreach ($neraca['ekuitas'] as $row)
        <tr>
            <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
            <td>{{number_format($row['nilai'], 2, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr>
            <td>Laba (Rugi) Tahun Berjalan</td>
            <td>{{number_format($neraca['labaBerjalan'], 2, ',', '.')}}</td>
        </tr>
        <tr>
            <td><b>Total Ekuitas</b></td>
            <td><b>{{number_format($neraca['totalEkuitas'] + $neraca['labaBerjalan'], 2, ',', '.')}}</b></td>
        </tr>
        <tr>
            <td><b>Total Liabilitas &amp; Ekuitas</b></td>
            <td><b>{{number_format($neraca['totalPasiva'], 2, ',', '.')}}</b></td>
        </tr>
    </tbody>
</table>
