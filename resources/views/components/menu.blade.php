<nav class="pcoded-navbar">
    <div class="nav-list">
        <div class="pcoded-inner-navbar main-menu">
            {{-- master --}}
            <div class="pcoded-navigation-label">Master</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="{{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ url('dashboard') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="feather icon-home"></i>
                        </span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                @if (Auth::user()->level == 'Administrator')
                    <li class="{{ Request::segment(1) == 'user' ? 'active' : '' }}">
                        <a href="{{ url('user') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon">
                                <i class="feather icon-user"></i>
                            </span>
                            <span class="pcoded-mtext">User</span>
                        </a>
                    </li>
                @endif
                {{-- master akuntansi --}}
                @if (Auth::user()->level != 'Viewer')

                <li class="{{ Request::segment(1) == 'supplier' ? 'active' : '' }}">
                    <a href="{{ url('supplier') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="fa-solid fa-people-carry-box"></i>
                        </span>
                        <span class="pcoded-mtext">Supplier</span>
                    </a>
                </li>

                <li class="{{ Request::segment(1) == 'customer' ? 'active' : '' }}">
                    <a href="{{ url('customer') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="fa-solid fa-user-tag"></i>
                        </span>
                        <span class="pcoded-mtext">Customer</span>
                    </a>
                </li>

                <li class="pcoded-hasmenu {{ Request::segment(1) == 'master-akuntasi' ? 'active' : '' }} {{ Request::segment(1) == 'master-akuntasi' ? 'pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="feather icon-bookmark"></i></span>
                        <span class="pcoded-mtext">Master Akuntasi</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ Request::segment(2) == 'kode-induk' ? 'active' : '' }}">
                            <a href="{{ url('master-akuntasi/kode-induk') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Kode Induk</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'kode-akun' ? 'active' : '' }}">
                            <a href="{{ url('master-akuntasi/kode-akun') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Kode Akun</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'kunci-transaksi' ? 'active' : '' }}">
                        <a href="{{ url('master-akuntasi/kunci-transaksi') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon">
                                <i class="feather icon-bookmark"></i>
                            </span>
                            <span class="pcoded-mtext">Kunci Transaksi</span>
                        </a>
                    </li>
                    </ul>
                </li>
                @endif
            </ul>
                {{-- <div class="pcoded-navigation-label">Master Akuntansi</div> --}}
            {{-- kepegawaian --}}
            @if (Auth::user()->level != 'Viewer')
            <div class="pcoded-navigation-label">Kepegawaian</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="{{ Request::segment(1) == 'karyawan' ? 'active' : '' }}">
                    <a href="{{ url('karyawan') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <span class="pcoded-mtext">Data Karyawan</span>
                    </a>
                </li>
                <li class="{{ Request::segment(1) == 'shift' ? 'active' : '' }}">
                    <a href="{{ url('shift') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <span class="pcoded-mtext">Master Shift</span>
                    </a>
                </li>
                <li class="{{ Request::segment(1) == 'jadwal-kerja' ? 'active' : '' }}">
                    <a href="{{ url('jadwal-kerja') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>
                        <span class="pcoded-mtext">Jadwal Kerja</span>
                    </a>
                </li>
            </ul>
            @endif
            <div class="pcoded-navigation-label">Transaksi Kas</div>
            {{-- Kas --}}
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ Request::segment(1) == 'kas' ? 'active' : '' }} {{ Request::segment(1) == 'kas' ? 'pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-wallet"></i></span>
                        <span class="pcoded-mtext">Kas</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ Request::segment(2) == 'kas-transaksi' ? 'active' : '' }}">
                            <a href="{{ url('kas/kas-transaksi') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Transaksi Kas</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'laporan-kas' ? 'active' : '' }}">
                            <a href="{{ url('kas/laporan-kas') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Laporan Kas</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            {{-- Bank --}}
            <div class="pcoded-navigation-label">Transaksi Bank</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ Request::segment(1) == 'bank' ? 'active' : '' }} {{ Request::segment(1) == 'bank' ? 'pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-credit-card"></i></span>
                        <span class="pcoded-mtext">Bank</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ Request::segment(2) == 'bank-transaksi' ? 'active' : '' }}">
                            <a href="{{ url('bank/bank-transaksi') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Transaksi Bank</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'laporan-bank' ? 'active' : '' }}">
                            <a href="{{ url('bank/laporan-bank') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Laporan Bank</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            {{-- Memorial Jurnal Umum --}}
            <div class="pcoded-navigation-label">Memorial / Jurnal Umum</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ Request::segment(1) == 'memorial' ? 'active' : '' }} {{ Request::segment(1) == 'memorial' ? 'pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-folder"></i></span>
                        <span class="pcoded-mtext">Memorial</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ Request::segment(2) == 'memorial' ? 'active' : '' }}">
                            <a href="{{ url('memorial/memorial') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Memorial / Jurnal Umum</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'laporan-memorial' ? 'active' : '' }}">
                            <a href="{{ url('memorial/laporan-memorial') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Laporan Memorial</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            {{-- General Ledger --}}
            <div class="pcoded-navigation-label">General Ledger</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ Request::segment(1) == 'general-ledger' ? 'active pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-book"></i></span>
                        <span class="pcoded-mtext">General Ledger</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ Request::segment(2) == 'buku-besar' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/buku-besar') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Buku Besar</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'neraca-saldo' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/neraca-saldo') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Neraca Saldo</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'laba-rugi' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/laba-rugi') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Laba Rugi</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'neraca' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/neraca') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Neraca</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'arus-kas' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/arus-kas') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Arus Kas</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'perubahan-ekuitas' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/perubahan-ekuitas') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Perubahan Ekuitas</span>
                            </a>
                        </li>
                        <li class="{{ Request::segment(2) == 'tutup-buku' ? 'active' : '' }}">
                            <a href="{{ url('general-ledger/tutup-buku') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <i class="feather icon-bookmark"></i>
                                </span>
                                <span class="pcoded-mtext">Tutup Buku</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            @if (Auth::user()->level != 'Viewer')
                {{-- user activity --}}
                <ul class="pcoded-item pcoded-left-item">
                    <li class="{{ Request::segment(1) == 'user-activity' ? 'active' : '' }}">
                        <a href="{{ url('user-activity') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon">
                                <i class="fa fa-history"></i>
                            </span>
                            <span class="pcoded-mtext">User Activity</span>
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</nav>
