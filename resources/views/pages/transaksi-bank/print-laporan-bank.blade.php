<center><h4>Laporan Bank</h4></center>
<center>
    Periode <strong>{{ \Request::get('start') }} s/d {{ \Request::get('end') }}</strong>
</center>
<table border="1">
    <thead style="background-color: rgba(47, 132, 165, 0.699)">
        <tr>
            <td>Tanggal</td>
            <td>Kode Transaksi</td>
            <td>Keterangan</td>
            <td>Pasangan</td>
            <td>Penerimaan</td>
            <td>Pengeluaran</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($report_kas as $item)
            <tr>
                <td>{{ $item->tanggal }}</td>
                <td>{{ $item->kode_transaksi_bank }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->kode_lawan }}</td>
                <td>
                    @if ($item->tipe == 'Masuk')
                        {{ $item->subtotal }}
                    @endif
                </td>
                <td>
                    @if ($item->tipe == 'Keluar')
                        {{ $item->subtotal }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@if (isset($_GET['xls']))
    @php
        $name = 'Laporan Kas ' . date('d-m-Y', strtotime($_GET['start'])).' s/d '.date('d-m-Y', strtotime($_GET['end'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif

