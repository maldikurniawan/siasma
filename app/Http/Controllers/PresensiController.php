<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $id = Auth::guard()->user()->id;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('id', $id)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $id = Auth::guard()->user()->id;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        // Lokasi Kampus
        $latitudekampus = -5.387261931715078;
        $longitudekampus = 105.21541890959807;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekampus, $longitudekampus, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);


        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $id . "-" . $tgl_presensi;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('id', $id)->count();
        if ($radius > 20) {
            echo "error|Maaf, Anda Berada Diluar Radius, Jarak Anda ".$radius."m Dari Lokasi|radius";
        } else {
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('id', $id)->update($data_pulang);
                if ($update) {
                    echo "success|Terima Kasih, Sampai Ketemu Lagi :)|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                }
            } else {
                $data = [
                    'id' => $id,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi
                ];
                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    echo "success|Terima Kasih, Selamat Belajar :)|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                }
            }
        }
    }

    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
