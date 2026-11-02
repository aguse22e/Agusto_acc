<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode Rekening</th>
                <th>Nama</th>
                <th>Kode Induk</th>
                <th>Tipe</th>
                <th>Deleted By</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach($kode_akun as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->kode_akun }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kodeInduk->kode_induk . ' - ' . $item->kodeInduk->nama }}</td>
                    <td>{{ $item->tipe }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('kodeAkun.restore', $item->kode_akun) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Restore" data-placement="top"><i class="ti-reload"></i></button>
                            </a>
                            {{-- <a href="{{ route('kode-induk.hapusPermanen',$item->kode_akun) }}"> --}}
                            <form action="{{ route('kodeAkun.hapusPermanen', [$item->kode_akun]) }}" method="post" onsubmit="return confirm('Delete this data permanently ?')">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" value="Delete" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus Permanen"
                                    data-placement="top">
                                    <span class="feather icon-trash"></span>
                                </button>
                                {{-- </a> --}}
                            </form>

                        </div>
                    </td>
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
    <div class="pull-right">
        {{ $kode_akun->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
