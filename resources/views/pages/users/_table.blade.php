<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($user as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->level }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('user.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="feather icon-edit"></span></button>
                            </a>
                            <form action="{{ route('user.destroy', $item->id) }}" method="post">
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
        {{ $user->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
