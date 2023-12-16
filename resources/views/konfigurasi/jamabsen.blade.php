@extends('dashboard.index')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/home" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Jam Absen</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection
@section('content')
    <div class="row" style="margin-top:4rem">
        <div class="col">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp
            @if (Session::get('success'))
                <div class="alert alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="alert alert-danger">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>
    <form method="POST" action="updatejamabsen">
        @csrf
        <div class="col">
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="text" value="{{ $jam_absen->matkul }}" id="matkul" name="matkul"
                        class="form-control" placeholder="Mata Kuliah">
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="time" value="{{ $jam_absen->awal_jam_masuk }}" id="awal_jam_masuk" name="awal_jam_masuk"
                        class="form-control" placeholder="Awal Jam Masuk">
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="time" value="{{ $jam_absen->jam_masuk }}" id="jam_masuk" name="jam_masuk"
                        class="form-control" placeholder="Jam Masuk">
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="time" value="{{ $jam_absen->akhir_jam_masuk }}" id="akhir_jam_masuk" name="akhir_jam_masuk"
                        class="form-control" placeholder="Akhir Jam Masuk">
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="time" value="{{ $jam_absen->jam_pulang }}" id="jam_pulang" name="jam_pulang"
                        class="form-control" placeholder="Jam Pulang">
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <button class="btn btn-primary w-100">
                        <ion-icon name="refresh-outline"></ion-icon>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
