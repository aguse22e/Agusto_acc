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

    @include('components.button-add', ['btnText' => $btnText, 'btnLink' => $btnLink])


    <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link {{ Request::segment(2) == 'kode-induk' ? 'active' : '' }}" href="{{ url('/master-akuntasi/kode-induk') }}">List Kode Induk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::segment(3) == 'trash' ? 'active' : '' }}" href="{{ url('/master-akuntasi/kode-induk/trash') }}">Tempat Sampah</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header">

            <h5>List Kode Induk</h5>
            <div class="col-md-4 pull-right">
                @include('components.search')
            </div>

        </div>
        <div class="card-block table-border-style">
            @include('pages.kode-induk._table')
        </div>
    </div>
@endsection
