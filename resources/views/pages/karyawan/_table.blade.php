<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th>Status</th>
                <th>Aktif</th>
                <th class="text-right">Gaji Pokok</th>
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
                    <td>{{ $item->departemen }}</td>
                    <td>{{ $item->status_karyawan }}</td>
                    <td>
                        @if ($item->aktif)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('karyawan.show', $item->id) }}" class="mr-2">
                                <button type="button" class="btn btn-info btn-sm"
                                    data-toggle="tooltip" title="Lihat" data-placement="top"><span
                                        class="feather icon-eye"></span></button>
                            </a>
                            <a href="{{ route('karyawan.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="feather icon-edit"></span></button>
                            </a>
                            <form action="{{ route('karyawan.destroy', $item->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus"
                                    data-placement="top"
                                    onclick="confirm('{{ __('Move data to trash ?') }}') ? this.parentElement.submit() : ''">
                                    <span class="feather icon-trash"></span>
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
