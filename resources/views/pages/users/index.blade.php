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
          <a class="nav-link {{ Request::segment(1) == 'user' ? 'active' : '' }}" href="{{ url('/user') }}">List User</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::segment(2) == 'trash' ? 'active' : '' }}" href="{{ url('/user/trash') }}">Tempat Sampah</a>
        </li>
    </ul>
    <div class="card">
        <div class="card-header">

            <h5>List User</h5>
            <div class="col-md-4 pull-right">
                @include('components.search')
            </div>

        </div>
        <div class="card-block table-border-style">
            @include('pages.users._table')
        </div>
    </div>
@endsection
