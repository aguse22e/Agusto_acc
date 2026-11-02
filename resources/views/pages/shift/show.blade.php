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

    <div class="card">
        <div class="card-block">
            <h4 class="sub-title">Detail Shift</h4>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Shift</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $shift->nama_shift }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jam Masuk</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $shift->jam_masuk }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jam Keluar</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $shift->jam_keluar }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $shift->keterangan }}" readonly>
                </div>
            </div>

            @include('components.button-list')
        </div>
    </div>

@endsection
