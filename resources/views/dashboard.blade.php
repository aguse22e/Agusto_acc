@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => 'Dashboard',
    'pageSubtitle' => '',
    'pageIcon' => 'feather icon-home',
    'parentMenu' => '',
    'current' => 'Dashboard'
    ])
@endsection

@section('content')
    @include('components.notification')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-b-25">Jumlah Transaksi Kas Masuk</h6>
                                    <h3 class="f-w-700 text-c-blue">Rp{{number_format($jumlahKasMasuk, 2, ',', '.')}}</h3>
                                    <p class="m-b-0">{{ date('F-Y') }}</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet bg-c-blue"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-b-25">Jumlah Transaksi Kas Keluar</h6>
                                    <h3 class="f-w-700 text-c-red">Rp{{number_format($jumlahKasKeluar, 2, ',', '.')}}</h3>
                                    <p class="m-b-0">{{ date('F-Y') }}</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet bg-c-red"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-b-25">Jumlah Transaksi Bank Masuk</h6>
                                    <h3 class="f-w-700 text-c-blue">Rp{{number_format($jumlahBankMasuk, 2, ',', '.')}}</h3>
                                    <p class="m-b-0">{{ date('F-Y') }}</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card bg-c-blue"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-b-25">Jumlah Transaksi Bank Keluar</h6>
                                    <h3 class="f-w-700 text-c-red">Rp{{number_format($jumlahBankKeluar, 2, ',', '.')}}</h3>
                                    <p class="m-b-0">{{ date('F-Y') }}</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card bg-c-red"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Transaksi Kas Terbaru</h5>
                    <h6>{{ date('F-Y') }}</h6>
                    <div class="card-header-right">
                        <ul class="list-unstyled card-option">
                            <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                            <li><i class="feather icon-maximize full-card"></i></li>
                            <li><i class="feather icon-minus minimize-card"></i></li>
                            {{-- <li><i class="feather icon-refresh-cw reload-card"></i></li> --}}
                            <li><i class="feather icon-trash close-card"></i></li>
                            <li><i class="feather icon-chevron-left open-card-option"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="card-block p-b-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Kode Akun</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksiKas as $item)
                                    <tr>
                                        <td>{{$item->kode_transaksi_kas}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->tanggal))}}</td>
                                        <td>{{$item->kodeAkun->kode_akun . ' - ' . $item->kodeAkun->nama}}</td>
                                        <td><label class="label label-{{$item->tipe == 'Masuk' ? 'success' : 'danger'}}">{{$item->tipe}}</label></td>
                                        <td>{{number_format($item->total, 2, ',', '.')}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Transaksi Bank Terbaru</h5>
                    <h6>{{ date('F-Y') }}</h6>
                    <div class="card-header-right">
                        <ul class="list-unstyled card-option">
                            <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                            <li><i class="feather icon-maximize full-card"></i></li>
                            <li><i class="feather icon-minus minimize-card"></i></li>
                            {{-- <li><i class="feather icon-refresh-cw reload-card"></i></li> --}}
                            <li><i class="feather icon-trash close-card"></i></li>
                            <li><i class="feather icon-chevron-left open-card-option"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="card-block p-b-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Kode Akun</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksiBank as $item)
                                    <tr>
                                        <td>{{$item->kode_transaksi_bank}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->tanggal))}}</td>
                                        <td>{{$item->kodeAkun->kode_akun . ' - ' . $item->kodeAkun->nama}}</td>
                                        <td><label class="label label-{{$item->tipe == 'Masuk' ? 'success' : 'danger'}}">{{$item->tipe}}</label></td>
                                        <td>{{number_format($item->total, 2, ',', '.')}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->level == 'Administrator')
            <div class="col-xl-4 col-md-6">
                <div class="card latest-update-card">
                    <div class="card-header">
                        <h5>Aktifitas Terbaru</h5>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                                <li><i class="feather icon-maximize full-card"></i></li>
                                <li><i class="feather icon-minus minimize-card"></i></li>
                                {{-- <li><i class="feather icon-refresh-cw reload-card"></i></li> --}}
                                <li><i class="feather icon-trash close-card"></i></li>
                                <li><i class="feather icon-chevron-left open-card-option"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="scroll-widget">
                            <div class="latest-update-box">
                                @foreach ($latestActivity as $item)
                                    <div class="row p-t-20">
                                        <div class="col-auto text-right update-meta p-r-0">
                                            <i class="b-{{$item->tipe == 'Insert' ? 'success' : ($item->tipe == 'Update' ? 'warning' : 'danger') }} update-icon ring"></i>
                                        </div>
                                        <div class="col p-l-5">
                                            <a href="#!">
                                                <h6>{{$item->tipe . ' ' . $item->jenis_transaksi}}</h6>
                                            </a>
                                            <p class="text-muted m-b-0">{{$item->keterangan}}</p>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- <div class="row p-b-30">
                                    <div class="col-auto text-right update-meta p-r-0">
                                        <i class="b-primary update-icon ring"></i>
                                    </div>
                                    <div class="col p-l-5">
                                        <a href="#!">
                                            <h6>Showcases</h6>
                                        </a>
                                        <p class="text-muted m-b-0">Lorem dolor sit amet, <a href="#!"
                                                class="text-c-blue">
                                                More</a></p>
                                    </div>
                                </div>
                                <div class="row p-b-30">
                                    <div class="col-auto text-right update-meta p-r-0">
                                        <i class="b-success update-icon ring"></i>
                                    </div>
                                    <div class="col p-l-5">
                                        <a href="#!">
                                            <h6>Miscellaneous</h6>
                                        </a>
                                        <p class="text-muted m-b-0">Lorem ipsum dolor sit ipsum amet, <a href="#!"
                                                class="text-c-green"> More</a></p>
                                    </div>
                                </div>
                                <div class="row p-b-30">
                                    <div class="col-auto text-right update-meta p-r-0">
                                        <i class="b-danger update-icon ring"></i>
                                    </div>
                                    <div class="col p-l-5">
                                        <a href="#!">
                                            <h6>Your Manager Posted.</h6>
                                        </a>
                                        <p class="text-muted m-b-0">Lorem ipsum dolor sit amet, <a href="#!"
                                                class="text-c-red"> More</a></p>
                                    </div>
                                </div>
                                <div class="row p-b-30">
                                    <div class="col-auto text-right update-meta p-r-0">
                                        <i class="b-primary update-icon ring"></i>
                                    </div>
                                    <div class="col p-l-5">
                                        <a href="#!">
                                            <h6>Showcases</h6>
                                        </a>
                                        <p class="text-muted m-b-0">Lorem dolor sit amet, <a href="#!"
                                                class="text-c-blue">
                                                More</a></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto text-right update-meta p-r-0">
                                        <i class="b-success update-icon ring"></i>
                                    </div>
                                    <div class="col p-l-5">
                                        <a href="#!">
                                            <h6>Miscellaneous</h6>
                                        </a>
                                        <p class="text-muted m-b-0">Lorem ipsum dolor sit ipsum amet, <a href="#!"
                                                class="text-c-green"> More</a></p>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
