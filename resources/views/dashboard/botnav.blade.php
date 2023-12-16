<div class="appBottomMenu">
    <a href="/home" class="item {{ request()->is('home') ? 'active' : '' }} ">
        <div class="col">
            <ion-icon name="home-outline"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="{{ url('presensi/histori') }}" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="desktop-outline" role="img" class="md hydrated"
                aria-label="desktop-outline"></ion-icon>
            <strong>Histori</strong>
        </div>
    </a>
    <a href="{{ url('presensi/create') }}" class="item">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="camera" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </div>
        </div>
    </a>
    <a href="{{ url('presensi/izin') }}" class="item {{ request()->is('presensi/izin') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="calendar-outline" role="img" class="md hydrated"
                aria-label="calendar-outline"></ion-icon>
            <strong>Izin</strong>
        </div>
    </a>
    <a href="/editProfile" class="item {{ request()->is('editProfile') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
            <strong>Profile</strong>
        </div>
    </a>
</div>
