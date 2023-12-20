@extends('dashboard.index')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/home" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Izin/Sakit</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <style>
        .historicontent {
            display: flex;
        }

        .datapresensi {
            margin-left: 10px;
        }

        .notif {
            position: absolute;
            right: 20px;
        }
    </style>
    <div class="row" style="margin-top:70px">
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
    <div class="row">
        <div class="col">
            @foreach ($dataizin as $d)
                @php
                    if ($d->status == 'i') {
                        $status = 'Izin';
                    } elseif ($d->status == 's') {
                        $status = 'Sakit';
                    } else {
                        $status = 'Not Found';
                    }
                @endphp
                <div class="card mb-1 card_izin" kode_izin="{{ $d->id }}" status_approved="{{ $d->status_approved }}"
                    data-toggle="modal" data-target="#actionSheetIconed">
                    <div class="card-body">
                        <div class="historicontent">
                            <div class="iconpresensi">
                                @if ($d->status == 'i')
                                    <ion-icon name="calendar-outline" style="font-size: 48px;"
                                        class="text-success"></ion-icon>
                                @else
                                    <ion-icon name="medkit-outline" style="font-size: 48px;"
                                        class="text-warning"></ion-icon>
                                @endif
                            </div>
                            <div class="datapresensi">
                                <h3 style="line-height: 15px">{{ date('d-m-Y', strtotime($d->tgl_izin)) }}
                                    ({{ $status }})
                                </h3>
                                <p>{{ $d->keterangan }}</p>

                            </div>
                            <div class="notif">
                                @if ($d->status_approved == 0)
                                    <span class="badge bg-warning">Waiting</span>
                                @elseif ($d->status_approved == 1)
                                    <span class="badge bg-success">Approved</span>
                                @elseif ($d->status_approved == 2)
                                    <span class="badge bg-danger">Decline</span>
                                @endif
                                @if ($d->status_approved == 0)
                                    <a href="#" class="badge bg-primary approve" id_izinsakit="{{ $d->id }}">
                                        ACC
                                    </a>
                                @else
                                    <a href="/presensi/{{ $d->id }}/batalkanizinsakit" class="badge bg-danger">
                                        Batal
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="fab-button bottom-right" style="margin-bottom:70px">
        <a href="buatizin" class="fab">
            <ion-icon name="add"></ion-icon>
        </a>
    </div>
    <div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Izin/Sakit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="approveizinsakit" method="POST">
                        @csrf
                        <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="status_approved" id="status_approved" class="form-select">
                                        <option value="1">Disetujui</option>
                                        <option value="2">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <ion-icon name="send-outline"></ion-icon>
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Pop UP Action --}}
    <div class="modal fade action-sheet" id="actionSheetIconed" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aksi</h5>
                </div>
                <div class="modal-body" id="showact">

                </div>
            </div>
        </div>
    </div>
    {{-- Delete --}}
    <div class="modal fade dialogbox" id="deleteConfirm" data-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yakin Dihapus ?</h5>
                </div>
                <div class="modal-body">
                    Data Pengajuan Izin Akan dihapus
                </div>
                <div class="modal-footer">
                    <div class="btn-inline">
                        <a href="#" class="btn btn-text-secondary" data-dismiss="modal">Batalkan</a>
                        <a href="" class="btn btn-text-primary" id="hapuspengajuan">Hapus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
        $(function() {
            $(".approve").click(function(e) {
                e.preventDefault();
                var id_izinsakit = $(this).attr("id_izinsakit");
                $("#id_izinsakit_form").val(id_izinsakit);
                $("#modal-izinsakit").modal("show");
            });
        });
    </script>
    <script>
        $(function() {
            $(".card_izin").click(function(e) {
                var kode_izin = $(this).attr("kode_izin");
                var status_approved = $(this).attr("status_approved");
                if (status_approved == 1) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Data Sudah Disetujui, Tidak Dapat Diubah!',
                        icon: 'warning'
                    })
                } else {
                    $("#showact").load('/izin/' + kode_izin + '/showact')
                }
            });
        });
    </script>
@endpush
