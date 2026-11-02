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
    @if (Auth::user()->level != 'Viewer')
        @include('components.button-add', ['btnText' => $btnText, 'btnLink' => $btnLink])
    @endif
    <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link {{ Request::segment(2) == 'memorial' ? 'active' : '' }}" href="{{ url('/memorial/memorial') }}">List Memorial</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::segment(3) == 'trash' ? 'active' : '' }}" href="{{ url('/memorial/memorial/trash') }}">Tempat Sampah</a>
        </li>
    </ul>
    <div class="card">
        <div class="card-header">

            <h5>{{ $pageTitle }}</h5>
            <div class="col-md-4 pull-right">
                @include('components.search')
            </div>

        </div>
        <div class="card-block table-border-style">
            @include('pages.memorial._table')
        </div>
    </div>
@endsection
