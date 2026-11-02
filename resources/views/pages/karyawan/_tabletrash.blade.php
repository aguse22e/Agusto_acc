<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($karyawan as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->kode_karyawan }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('karyawan.restore', $item->id) }}" class="mr-2">
                                <button type="button" class="btn btn-success btn-sm"
                                    data-toggle="tooltip" title="Restore" data-placement="top"><span
                                        class="feather icon-rotate-ccw"></span></button>
                            </a>
                            <form action="{{ route('karyawan.hapusPermanen', $item->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus Permanen"
                                    data-placement="top"
                                    onclick="confirm('{{ __('Delete data permanently ?') }}') ? this.parentElement.submit() : ''">
                                    <span class="feather icon-trash-2"></span>
                                </button>
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
        {{ $karyawan->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
