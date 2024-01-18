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
                    <div class="card">
                        <div class="data_table">
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
    </div>
    <script src="{{ asset('tabler/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('tabler/dist/js/jquery-3.6.0.min.js') }}"></script>
@endsection
