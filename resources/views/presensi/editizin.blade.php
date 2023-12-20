@extends('dashboard.index')
@section('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <style>
        .datepicker-modal {
            max-height: 465px !important;
        }
    </style>
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="izin" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Form Izin</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection
@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/izin/{{$dataizin->id}}/update" id="frmizin">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_izin" value="{{ $dataizin->tgl_izin }}" name="tgl_izin"
                        class="form-control datepicker" placeholder="Tanggal">
                </div>
                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="{{ $dataizin->status }}" hidden>{{ $dataizin->status }}</option>
                        <option value="i">Izin</option>
                        <option value="s">Sakit</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" name="keterangan" id="keterangan" value="{{ $dataizin->keterangan }}"
                        class="form-control" autocomplete="off" placeholder="Keterangan">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary w-100">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        var currYear = (new Date()).getFullYear();

        $(document).ready(function() {
            $(".datepicker").datepicker({
                format: "yyyy-mm-dd"
            });

            $("#tgl_izin").change(function(e) {
                var tgl_izin = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '/presensi/cekpengajuanizin',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_izin: tgl_izin
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond == 1) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Anda Sudah Mengajukan Izin Pada Tanggal Tersebut!',
                                icon: 'warning'
                            }).then((result) => {
                                $("#tgl_izin").val("");
                            });
                        }
                    }
                });
            });

            $("#frmizin").submit(function() {
                var tgl_izin = $("#tgl_izin").val();
                var status = $("#status").val();
                var keterangan = $("#keterangan").val();
                if (tgl_izin == "") {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Tanggal Harus Diisi!',
                        icon: 'warning'
                    });
                    return false;
                } else if (status == "") {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Status Harus Diisi!',
                        icon: 'warning'
                    });
                    return false;
                } else if (keterangan == "") {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Keterangan Harus Diisi!',
                        icon: 'warning'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
