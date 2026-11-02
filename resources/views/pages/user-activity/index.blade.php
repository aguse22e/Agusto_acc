@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => $pageTitle,
    'pageSubtitle' => '',
    'pageIcon' => $pageIcon,
    'parentMenu' => $parentMenu,
    'current' => $current
    ])
@endsection

@section('content')

    @include('components.notification')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4><strong> User Activity </strong></h4>
                </div>
                <div class="card-block">
                    <form  action="{{ route('user-activity.index') }}"  method="GET" class="mb-5" id="report_kas">
                        <div class="form-group row ">
                            <div class="col-lg-3">
                                <label class="col-form-label">Dari</label>
                                <input class="form-control form-control-lg {{ $errors->has('start') ? ' is-invalid' : '' }}" type="date" name="start" value="{{ old('start', isset($_GET['start']) != null ? $_GET['start'] : date('Y-m-d')) }}" placeholder="Tanggal"/>
                                @error('start')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-3">
                                <label class="col-form-label">Sampai</label>
                                <input class="form-control form-control-lg {{ $errors->has('end') ? ' is-invalid' : '' }}" type="date" name="end" value="{{ old('end', isset($_GET['end']) != null ? $_GET['end'] : date('Y-m-d')) }}" />
                                @error('end')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-3">
                                <label class="col-form-label">Semua User</label>
                                <select name="id_user" id="id_user" class="select2 form-control js-example-basic-single {{ $errors->has('id_user') ? ' is-invalid' : '' }}" style="width: 100%">
                                    <option value="0"> --Semua User--</option>
                                    @foreach ($users as $item)
                                        <option value="{{$item->id}}" {{isset($_GET['id_user']) && $_GET['id_user'] == $item->id ? 'selected' : ''}} >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-12 mt-4" style="padding-top: 10px">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                                {{-- <div class="d-flex justify-content-end"> --}}
                                    {{-- <div class="mt-4" style="p"> --}}

                                    {{-- </div> --}}

                                {{-- </div> --}}
                            </div>
                        </div>
                    </form>
                    {{-- <h4 class="sub-title">Basic Inputs</h4> --}}
                    <div class="table-responsive">
                        <table class="table table-styling table-de">
                            <thead>
                                <tr class="table-primary">
                                    <td>#</td>
                                    <td>User</td>
                                    <td>Jenis Transaksi</td>
                                    <td>Tipe</td>
                                    <td>Keterangan</td>
                                    <td>Waktu</td>
                                    <td>Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $page = Request::get('page');
                                    $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                                @endphp
                                @foreach ($logActivity as $item)
                                    <tr class="border-bottom-primary">
                                        <td class="text-center text-muted">{{ $no }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->jenis_transaksi }}</td>
                                        <td>{{ $item->tipe }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{date('d-m-Y H:i:s', strtotime($item->created_at))}}</td>
                                        <td>
                                            <a href="{{ route('user-activity.show', $item->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Lihat</a>
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pull-right">
                            {{ $logActivity->appends(Request::all())->links('vendor.pagination.custom') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
