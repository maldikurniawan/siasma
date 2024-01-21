@extends('dashboard.admin.index')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        Approve Izin
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="/presensi/izinsakit" method="GET">
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="input-icon mb-3">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-calendar-month"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                            <path d="M16 3v4" />
                                                            <path d="M8 3v4" />
                                                            <path d="M4 11h16" />
                                                            <path d="M7 14h.013" />
                                                            <path d="M10.01 14h.005" />
                                                            <path d="M13.01 14h.005" />
                                                            <path d="M16.015 14h.005" />
                                                            <path d="M13.015 17h.005" />
                                                            <path d="M7.01 17h.005" />
                                                            <path d="M10.01 17h.005" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" id="tanggal" name="tanggal"
                                                        value="{{ Request('tanggal') }}" class="form-control"
                                                        placeholder="Tanggal Presensi" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-icon mb-3">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-user" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" name="name" id="name"
                                                        value="{{ Request('name') }}" class="form-control"
                                                        placeholder="Nama Mahasiswa">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <button class="btn btn-primary" type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-search" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                            <path d="M21 21l-6 -6" />
                                                        </svg>
                                                        Cari
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No. </th>
                                                <th>Tanggal</th>
                                                <th>NPM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                                <th>Status Approve</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($izinsakit as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration + $izinsakit->firstItem() - 1 }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($d->tgl_izin)) }}</td>
                                                    <td>{{ $d->npm }}</td>
                                                    <td>{{ $d->name }}</td>
                                                    <td>{{ $d->status == 'i' ? 'Izin' : 'Sakit' }}</td>
                                                    <td>{{ $d->keterangan }}</td>
                                                    <td>
                                                        @if ($d->status_approved == 1)
                                                            <span class="badge bg-success">Disetujui</span>
                                                        @elseif($d->status_approved == 2)
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($d->status_approved == 0)
                                                            <a href="#" class="btn btn-sm btn-primary approve"
                                                                id_izinsakit="{{ $d->kode_izin }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-external-link"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                                    <path d="M11 13l9 -9" />
                                                                    <path d="M15 4h5v5" />
                                                                </svg>
                                                            </a>
                                                        @else
                                                            <a href="/presensi/{{ $d->kode_izin }}/batalkanizinsakit"
                                                                class="btn btn-sm btn-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-circle-x"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                                    <path d="M10 10l4 4m0 -4l-4 4" />
                                                                </svg>
                                                                Batalkan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $izinsakit->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Izin/Sakit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/presensi/approveizinsakit" method="POST">
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
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 14l11 -11" />
                                            <path
                                                d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $(".approve").click(function(e) {
                e.preventDefault();
                var id_izinsakit = $(this).attr("id_izinsakit");
                $("#id_izinsakit_form").val(id_izinsakit);
                $("#modal-izinsakit").modal("show");
            });

            $("#tanggal").datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd"
            });
        });
    </script>
@endpush
