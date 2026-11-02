<table border="1">
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Laporan Arus Kas</b></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Periode {{date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))}}</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach (['operasi' => 'Aktivitas Operasi', 'investasi' => 'Aktivitas Investasi', 'pendanaan' => 'Aktivitas Pendanaan'] as $kunci => $judul)
        <tr><td colspan="2"><b>{{$judul}}</b></td></tr>
        @foreach ($arusKas[$kunci] as $row)
        <tr>
            <td>{{$row['akun']->kode_akun . ' ~ ' . $row['akun']->nama}}</td>
            <td>{{number_format($row['nilai'], 2, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr>
            <td><b>Kas Bersih dari {{$judul}}</b></td>
            <td><b>{{number_format($arusKas['total' . ucfirst($kunci)], 2, ',', '.')}}</b></td>
        </tr>
        @endforeach
        <tr>
            <td><b>Kenaikan (Penurunan) Bersih Kas</b></td>
            <td><b>{{number_format($arusKas['kenaikanBersih'], 2, ',', '.')}}</b></td>
        </tr>
        <tr>
            <td>Kas &amp; Setara Kas Awal Periode</td>
            <td>{{number_format($arusKas['kasAwal'], 2, ',', '.')}}</td>
        </tr>
        <tr>
            <td><b>Kas &amp; Setara Kas Akhir Periode</b></td>
            <td><b>{{number_format($arusKas['kasAkhir'], 2, ',', '.')}}</b></td>
        </tr>
    </tbody>
</table>
