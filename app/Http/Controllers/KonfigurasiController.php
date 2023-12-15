<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{
    public function lokasiabsen()
    {
        $lok_absen = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasiabsen', compact('lok_absen'));
    }

    public function updatelokasi(Request $request)
    {
        $lokasi_absen = $request->lokasi_absen;
        $radius = $request->radius;

        $update = DB::table('konfigurasi_lokasi')->where('id', 1)->update([
            'lokasi_absen' => $lokasi_absen,
            'radius' => $radius
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data gagal Diupdate']);
        }
    }
}
