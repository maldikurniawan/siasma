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
                {{-- @if (!empty(Auth::guard()->user()->foto))
                    @php
                        $path = Storage::url('uploads/mahasiswa/' . Auth::guard()->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64 rounded" style="height:60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif --}}
                <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
            </div>
            <div id="user-info">
                <h3 id="user-name">{{ Auth::guard()->user()->name }}</h3>
                {{-- <span id="user-prodi">{{ Auth::guard()->user()->npm }}</span><br> --}}
                <span id="user-prodi">2015061071</span><br>
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
                            <a href="{{ url('presensi/create') }}" class="orange" style="font-size: 40px;">
                                <ion-icon name="camera"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            Presensi
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="section mt-2" id="presence-section">
    </div>
@endsection
