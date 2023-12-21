@if ($histori->isEmpty())
    <div class="alert alert-warning">
        <p>Data Belum Ada</p>
    </div>
@endif
<style>
    .historicontent {
        display: flex;
    }

    .datapresensi {
        margin-left: 10px;
    }
</style>
@foreach ($histori as $d)
    @if ($d->status == 'h')
        <div class="card mb-1">
            <div class="card-body">
                <div class="historicontent">
                    <div class="iconpresensi">
                        <ion-icon name="person-outline" style="font-size: 48px;" class="text-primary"></ion-icon>
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
