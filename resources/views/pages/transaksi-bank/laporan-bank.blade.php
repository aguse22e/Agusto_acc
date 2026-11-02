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
                    <h4><strong> Laporan Bank </strong></h4>
                </div>
                <div class="card-block">
                    <form action="{{ route('laporan-bank') }}" method="GET" class="mb-5" id="report_bank">
                        <div class="form-group row ">
                            <div class="col-lg-4">
                                <label class="col-form-label">Kode Akun Bank</label>
                                <select name="kode_perkiraan" id="kode_perkiraan" class="select2 form-control js-example-basic-single {{ $errors->has('kode_perkiraan') ? ' is-invalid' : '' }}" style="width: 100%">
                                    <option value="0"> --Pilih Kode Akun--</option>
                                    @foreach ($kodeAkun as $item)
                                        @if (isset($_GET['kode_perkiraan']))
                                            <option value="{{$item->kode_akun}}" {{ old('kode_perkiraan', $_GET['kode_perkiraan'] == $item->kode_akun ? 'selected' : '') }} >{{$item->kode_akun . ' -- '. $item->nama}}</option>
                                        @else
                                            <option value="{{$item->kode_akun}}" {{ old('kode_perkiraan') }} >{{$item->kode_akun . ' -- '. $item->nama}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('kode_perkiraan'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('kode_perkiraan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-4">
                                <label class="col-form-label">Dari</label>
                                <input class="form-control form-control-lg {{ $errors->has('start') ? ' is-invalid' : '' }}" type="date" name="start" value="{{ old('start', isset($_GET['start']) != null ? $_GET['start'] : date('Y-m-d')) }}"/>
                                @error('start')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-4">
                                <label class="col-form-label">Sampai</label>
                                <input class="form-control form-control-lg {{ $errors->has('end') ? ' is-invalid' : '' }}" type="date" name="end" value="{{ old('end', isset($_GET['end']) != null ? $_GET['end'] : date('Y-m-d')) }}" />
                                @error('end')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-end">
                                    <div class="mt-4" style="p">
                                        <button type="submit" class="btn btn-primary "><i class="fas fa-filter"></i> Filter</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- <h4 class="sub-title">Basic Inputs</h4> --}}
                    @if ($report_bank != null)
                        <div class="row d-flex justify-content-between">
                            <div class="col">
                                <h6 class="mb-3">Laporan Bank</h6>
                                <h6 class="mb-3">Periode <strong class="font-weight-bold"> {{ \Request::get('start') }} s/d {{ \Request::get('end') }} </strong></h6>
                                <h6 class="mb-3">
                                    @foreach ($kodeAkun as $item)
                                        @if ($_GET['kode_perkiraan'] == $item->kode_akun)
                                        {{ $_GET['kode_perkiraan'].' '. $item->nama }}
                                        @endif
                                    @endforeach
                                </h6>
                            </div>
                            <div class="form-group mr-3">
                                <a target="_blank" href="{{ route('print-bank')."?start=$_GET[start]&end=$_GET[end]&kode_perkiraan=$_GET[kode_perkiraan]" }}"  class="btn btn-danger waves-effect waves-light text-white"><i class="fa fa-file-pdf-o"></i> Download PDF</a>
                                <a target="_blank" href="{{ route('print-bank')."?start=$_GET[start]&end=$_GET[end]&kode_perkiraan=$_GET[kode_perkiraan]&xls=true" }}" class="btn btn-success waves-effect waves-light text-white"><i class="fa fa-file-excel-o"></i> Download Excel</a>

                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-styling table-de">
                                <thead>
                                    <tr class="table-primary">
                                        <td>Tanggal</td>
                                        <td>Kode Transaksi</td>
                                        <td>Keterangan</td>
                                        <td>Pasangan</td>
                                        <td>Penerimaan</td>
                                        <td>Pengeluaran</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $page = Request::get('page');
                                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                                    @endphp --}}
                                    @foreach ($report_bank as $item)
                                        <tr class="border-bottom-primary">
                                            <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $item->kode_transaksi_bank }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>{{ $item->kode_lawan }}</td>
                                            <td>
                                                @if ($item->tipe == 'Masuk')
                                                    Rp. {{number_format($item->subtotal, 2, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->tipe == 'Keluar')
                                                    Rp. {{number_format($item->subtotal, 2, ',', '.') }}
                                                @endif
                                            </td>

                                        </tr>
                                        {{-- @php
                                            $no++;
                                        @endphp --}}
                                    @endforeach
                                </tbody>
                            </table>
                            <
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
