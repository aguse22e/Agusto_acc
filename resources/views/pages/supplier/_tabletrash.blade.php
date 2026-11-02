<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No Handphone</th>
                <th>Deleted At</th>
                <th>Deleted By</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($supplier as $item)
                @php
                    $nama = \App\Models\User::select('*')->where('id',$item->deleted_by)->first();

                @endphp
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->deleted_at }}</td>
                    <td>{{ $nama->name }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('supplier.restore', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Restore" data-placement="top"><i class="ti-reload"></i></button>
                            </a>
                            {{-- <a href="{{ route('supplier.hapusPermanen',$item->id) }}"> --}}
                            <form action="{{ route('supplier.hapusPermanen', [$item->id]) }}" method="post" onsubmit="return confirm('Delete this data permanently ?')">
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
        {{ $supplier->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
