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
            <h4 class="sub-title">Detail Supplier</h4>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->kode_supplier }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->nama }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kontak Person</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->kontak_person }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">No HP / Telepon</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->no_hp . ' / ' . $supplier->telepon }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->email }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->alamat }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kota / Provinsi / Kode Pos</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->kota . ' / ' . $supplier->provinsi . ' / ' . $supplier->kode_pos }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">NPWP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->npwp }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Termin Pembayaran</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->termin_hari . ' hari' }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Saldo Awal Hutang</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ 'Rp. ' . number_format($supplier->hutang, 2, ',', '.') }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Bank Pembayaran</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->nama_bank . ' ' . $supplier->no_rekening }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Akun Hutang Default</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->akun_hutang }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Catatan</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $supplier->catatan }}" readonly>
                </div>
            </div>

            @include('components.button-list')
        </div>
    </div>

@endsection
