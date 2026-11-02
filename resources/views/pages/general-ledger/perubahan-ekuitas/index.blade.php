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
        <div class="card-header">
            <h5>Laporan Perubahan Ekuitas</h5>
        </div>
        <div class="card-body">
            @include('pages.general-ledger.perubahan-ekuitas._form-filter')
        </div>

        @if (isset($perubahan) && isset($dari) && isset($sampai))
        @include('pages.general-ledger.perubahan-ekuitas._show-perubahan-ekuitas')
        @endif
    </div>
@endsection
