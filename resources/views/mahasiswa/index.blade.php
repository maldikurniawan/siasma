@extends('dashboard.admin.index')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        Data Mahasiswa
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            @if (Session::get('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif

                            @if (Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{ Session::get('warning') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="#" class="btn btn-primary" id="tambahMahasiswa">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="data_table mt-2">
                        <table id="example" class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NPM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>No. HP</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mahasiswa as $d)
                                    @php
                                        $path = Storage::url('uploads/mahasiswa/' . $d->foto);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->npm }}</td>
                                        <td>{{ $d->name }}</td>
                                        <td>{{ $d->prodi }}</td>
                                        <td>{{ $d->no_hp }}</td>
                                        <td>
                                            @if (empty($d->foto))
                                                <img src="{{ asset('assets/img/nophoto.jpg') }}" class="avatar"
                                                    alt="">
                                            @else
                                                <img src="{{ url($path) }}" class="avatar" alt="">
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal --}}
    <div class="modal modal-blur fade" id="modal-inputmahasiswa" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/mahasiswa/store" method="POST" id="frmsiswa" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-barcode"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                                            <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                            <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                                            <path d="M5 11h1v2h-1z" />
                                            <path d="M10 11l0 2" />
                                            <path d="M14 11h1v2h-1z" />
                                            <path d="M19 11l0 2" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="npm" name="npm" class="form-control"
                                        placeholder="NPM">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="name" name="name"
                                        class="form-control" placeholder="Nama Lengkap">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                    </span>
                                    <input type="email" value="" id="email" name="email"
                                        class="form-control" placeholder="Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                            <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                            <path d="M3 6l0 13" />
                                            <path d="M12 6l0 13" />
                                            <path d="M21 6l0 13" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="prodi" name="prodi"
                                        class="form-control" placeholder="Prodi">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-device-mobile" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" />
                                            <path d="M11 4h2" />
                                            <path d="M12 17v.01" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="no_hp" name="no_hp"
                                        class="form-control" placeholder="No. HP">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <input type="file" name="foto" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100">
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
    {{-- <script src="{{ asset('tabler/dist/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script src="{{ asset('tabler/dist/js/jquery-3.6.0.min.js') }}"></script>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#tambahMahasiswa").click(function() {
                $("#modal-inputmahasiswa").modal("show");
            });

            $("#frmsiswa").submit(function() {
                var npm = $("#npm").val();
                var name = $("#name").val();
                var prodi = $("#prodi").val();
                var no_hp = $("#no_hp").val();
                if (npm == "") {
                    // alert('NPM Harus Diisi!');
                    Swal.fire({
                        title: 'Warning!',
                        text: 'NPM Harus Diisi!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    $("#npm").focus();
                    return false;
                } else if (name == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Nama Harus Diisi!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    $("#name").focus();
                    return false;
                } else if (email == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Email Harus Diisi!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    $("#email").focus();
                    return false;
                } else if (prodi == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Prodi Harus Diisi!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    $("#prodi").focus();
                    return false;
                } else if (no_hp == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'No HP Harus Diisi!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    $("#no_hp").focus();
                    return false;
                }
            });
        });
    </script>
@endpush
