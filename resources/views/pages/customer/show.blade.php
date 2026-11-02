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
            <h4 class="sub-title">Detail Customer</h4>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->kode_customer }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->nama }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kontak Person</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->kontak_person }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">No HP / Telepon</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->no_hp . ' / ' . $customer->telepon }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->email }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->alamat }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kota / Provinsi / Kode Pos</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->kota . ' / ' . $customer->provinsi . ' / ' . $customer->kode_pos }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">NPWP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->npwp }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Termin Pembayaran</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->termin_hari . ' hari' }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Saldo Awal Piutang</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ 'Rp. ' . number_format($customer->piutang, 2, ',', '.') }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->kategori }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Limit Kredit</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ 'Rp. ' . number_format($customer->limit_kredit, 2, ',', '.') }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Akun Piutang Default</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->akun_piutang }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Catatan</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $customer->catatan }}" readonly>
                </div>
            </div>

            @include('components.button-list')
        </div>
    </div>

@endsection
