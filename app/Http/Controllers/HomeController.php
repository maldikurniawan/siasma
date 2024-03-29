<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1;
        $tahunini = date("Y");
        $id = Auth::guard()->user()->id;
        $presensihariini = DB::table('presensi')->where('users_id', $id)->where('tgl_presensi', $hariini)->first();
        $historibulanini = DB::table('presensi')
            ->leftJoin('jam_absen', 'presensi.jam_absen_id', '=', 'jam_absen.id')
            ->where('users_id', $id)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('SUM(IF(status="h",1,0)) as jmlhadir, SUM(IF(jam_in > jam_masuk,1,0)) as jmlterlambat')
            ->leftJoin('jam_absen', 'presensi.jam_absen_id', '=', 'jam_absen.id')
            ->where('users_id', $id)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();

        $leaderboard = DB::table('presensi')
            ->leftJoin('jam_absen', 'presensi.jam_absen_id', '=', 'jam_absen.id')
            ->join('users', 'users.id', '=', 'presensi.users_id')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('users_id', $id)
            ->whereRaw('MONTH(tgl_izin)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_izin)="' . $tahunini . '"')
            ->where('status_approved', 1)
            ->first();

        return view('home.index', compact('presensihariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'leaderboard', 'rekapizin'));
    }

    public function homeadmin()
    {
        $hariini = date("Y-m-d");
        $rekappresensi = DB::table('presensi')
            ->selectRaw('SUM(IF(status="h",1,0)) as jmlhadir, SUM(IF(jam_in > jam_masuk,1,0)) as jmlterlambat')
            ->leftJoin('jam_absen', 'presensi.jam_absen_id', '=', 'jam_absen.id')
            ->where('tgl_presensi', $hariini)
            ->first();

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();
        return view('home.homeadmin', compact('rekappresensi', 'rekapizin'));
    }
}
