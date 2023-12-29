@extends('dashboard.index')
@section('content')
    <style>
        .logout {
            position: absolute;
            color: white;
            font-size: 30px;
            text-decoration: none;
            right: 8px;
        }

        .logout:hover {
            color: white;
        }
    </style>
    <div class="section" id="user-section">
        <a href="{{ route('logout') }}" class="logout"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <ion-icon name="exit-outline"></ion-icon>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <div id="user-detail">
            <div class="avatar">
                @if (!empty(Auth::guard()->user()->foto))
                    @php
                        $path = Storage::url('uploads/mahasiswa/' . Auth::guard()->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64 rounded" style="height:60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif
            </div>
            <div id="user-info">
                <h3 id="user-name">{{ Auth::guard()->user()->name }}</h3>
                <span id="user-prodi">{{ Auth::guard()->user()->prodi }}</span><br>
                <span id="user-role">Logged in as {{ Auth::user()->role }}</span>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/editProfile" class="green" style="font-size: 40px;">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Profil</span>
                        </div>
                    </div>
                    @if (Auth::user()->role != 'mahasiswa')
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/presensi/rekap" class="danger" style="font-size: 40px;">
                                    <ion-icon name="document-attach"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Rekap</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/konfigurasi/jamabsen" class="warning" style="font-size: 40px;">
                                    <ion-icon name="alarm"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Jam</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/konfigurasi/lokasiabsen" class="orange" style="font-size: 40px;">
                                    <ion-icon name="location"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                Lokasi
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role == 'mahasiswa')
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="{{ url('presensi/izin') }}" class="danger" style="font-size: 40px;">
                                    <ion-icon name="calendar"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Izin</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="{{ url('presensi/histori') }}" class="warning" style="font-size: 40px;">
                                    <ion-icon name="desktop"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Histori</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="#" class="orange" style="font-size: 40px;">
                                    <ion-icon name="book"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                Matkul
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">
                <div class="col-6">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $presensihariini->foto_in);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Masuk</h4>
                                    <span>{{ $presensihariini != null ? date('H:i', strtotime($presensihariini->jam_in)) : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null && $presensihariini->jam_out != null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $presensihariini->foto_out);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Pulang</h4>
                                    <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? date('H:i', strtotime($presensihariini->jam_out)) : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="rekappresensi">
            <h3>Rekap Presensi {{ $namabulan[$bulanini] }} {{ $tahunini }}</h3>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:3px; font-size:0.6rem; z-index:999">{{ $rekappresensi->jmlhadir }}</span>
                            <ion-icon name="accessibility-outline" style="font-size: 1.6rem;"
                                class="text-primary"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:3px; font-size:0.6rem; z-index:999">{{ $rekapizin->jmlizin }}</span>
                            <ion-icon name="calendar-outline" style="font-size: 1.6rem;" class="text-success"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Izin</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:3px; font-size:0.6rem; z-index:999">{{ $rekapizin->jmlsakit }}</span>
                            <ion-icon name="medkit-outline" style="font-size: 1.6rem;" class="text-warning"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:3px; font-size:0.6rem; z-index:999">{{ $rekappresensi->jmlterlambat }}</span>
                            <ion-icon name="alarm-outline" style="font-size: 1.6rem;" class="text-danger"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Telat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="presencetab mt-2">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                            Bulan Ini
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                            Leaderboard
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:100px;">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <style>
                        .historicontent {
                            display: flex;
                        }

                        .datapresensi {
                            margin-left: 10px;
                        }
                    </style>
                    @foreach ($historibulanini as $d)
                        @if ($d->status == 'h')
                            <div class="card mb-1">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="person-outline" style="font-size: 48px;"
                                                class="text-primary"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3 style="line-height: 3px">{{ $d->matkul }}</h3>
                                            <h4 style="margin: 0px; !important">
                                                {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                                            <span>
                                                {!! $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '<span class="text-danger">Belum Scan</span>' !!}
                                            </span>
                                            <span>
                                                {!! $d->jam_out != null
                                                    ? '-' . date('H:i', strtotime($d->jam_out))
                                                    : '<span class="text-danger">- Belum Scan</span>' !!}
                                            </span>
                                            <br>
                                            <span>{!! date('H:i', strtotime($d->jam_in)) > date('H:i', strtotime($d->jam_masuk))
                                                ? '<span class="text-danger">Terlambat</span>'
                                                : '<span class="text-success">Tepat Waktu</span>' !!}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <ul class="listview image-listview">
                        @foreach ($leaderboard as $d)
                            @if ($d->status == 'h')
                                <li>
                                    <div class="item">
                                        <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                                        <div class="in">
                                            <div>
                                                <b>{{ $d->name }}</b><br>
                                                <small class="text-muted">{{ $d->prodi }}</small>
                                            </div>
                                            <span
                                                class="badge {{ $d->jam_in < $d->jam_masuk ? 'bg-success' : 'bg-danger' }}">
                                                {{ date('H:i', strtotime($d->jam_in)) }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
@endsection
