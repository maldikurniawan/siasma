<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $id = Auth::guard()->user()->id;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('users_id', $id)->count();
        $lok_absen = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $jam_absen = DB::table('jam_absen')->where('id', 1)->first();
        return view('presensi.create', compact('cek', 'lok_absen', 'jam_absen'));
    }

    public function store(Request $request)
    {
        $id = Auth::guard()->user()->id;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        // Lokasi Kampus
        $lok_absen = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $lok = explode(",", $lok_absen->lokasi_absen);
        $latitudekampus = $lok[0];
        $longitudekampus = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekampus, $longitudekampus, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);
        $jam_absen = DB::table('jam_absen')->where('id', 1)->first();

        $presensi = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('users_id', $id);
        $cek = $presensi->count();
        $datapresensi = $presensi->first();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $id . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($radius > $lok_absen->radius) {
            echo "error|Maaf, Anda Berada Diluar Radius, Jarak Anda " . $radius . "m Dari Lokasi|radius";
        } else {
            if ($cek > 0) {
                if ($jam < $jam_absen->jam_pulang) {
                    echo "error|Maaf, Belum Waktunya Pulang|out";
                } else if (!empty($datapresensi->jam_out)) {
                    echo "error|Anda Sudah Melakukan Absen!|out";
                } else {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $fileName,
                        'lokasi_out' => $lokasi
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('users_id', $id)->update($data_pulang);
                    if ($update) {
                        echo "success|Terima Kasih, Sampai Jumpa :)|out";
                        Storage::put($file, $image_base64);
                    } else {
                        echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                    }
                }
            } else {
                if ($jam < $jam_absen->awal_jam_masuk) {
                    echo "error|Maaf, Belum Waktunya Presensi|in";
                } else if ($jam > $jam_absen->akhir_jam_masuk) {
                    echo "error|Maaf, Waktu Presensi Sudah Habis|in";
                } else {
                    $data = [
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'status' => 'h',
                        'jam_absen_id' => $jam_absen->id,
                        'users_id' => Auth::user()->id
                    ];
                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo "success|Terima Kasih, Selamat Belajar :)|in";
                        Storage::put($file, $image_base64);
                    } else {
                        echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|in";
                    }
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

    public function editProfile()
    {
        $id = Auth::guard()->user()->id;
        $users = DB::table('users')->where('id', $id)->first();
        return view('presensi.editProfile', compact('users'));
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::guard()->user()->id;
        $name = $request->name;
        $prodi = $request->prodi;
        $npm = $request->npm;
        $no_hp = $request->no_hp;
        $email = $request->email;
        $password = Hash::make($request->password);
        $users = DB::table('users')->where('id', $id)->first();
        $request->validate([
            'foto' => 'required|image|mimes:png,jpg,jpeg'
        ]);
        if ($request->hasFile('foto')) {
            $foto = $id . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $users->foto;
        }

        if (empty($request->password)) {
            $data = [
                'name' => $name,
                'prodi' => $prodi,
                'npm' => $npm,
                'no_hp' => $no_hp,
                'email' => $email,
                'foto' => $foto
            ];
        } else {
            $data = [
                'name' => $name,
                'prodi' => $prodi,
                'npm' => $npm,
                'no_hp' => $no_hp,
                'email' => $email,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('users')->where('id', $id)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/mahasiswa/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['error' => 'Data Gagal Di Update']);
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $id = Auth::guard()->user()->id;

        $histori = DB::table('presensi')
            ->leftJoin('jam_absen', 'presensi.jam_absen_id', '=', 'jam_absen.id')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('users_id', $id)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function izin()
    {
        $id = Auth::guard()->user()->id;
        $dataizin = DB::table('pengajuan_izin')->where('users_id', $id)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $id = Auth::guard()->user()->id;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan,
            'users_id' => Auth::user()->id
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('presensi/izin')->with(['success' => 'Data Gagal Disimpan']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $id = Auth::guard()->user()->id;

        $cek = DB::table('pengajuan_izin')->where('users_id', $id)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('presensi')
            ->selectRaw('presensi.users_id,name,npm,
                MAX(IF(DAY(tgl_presensi) = 1,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_1,
                MAX(IF(DAY(tgl_presensi) = 2,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_2,
                MAX(IF(DAY(tgl_presensi) = 3,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_3,
                MAX(IF(DAY(tgl_presensi) = 4,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_4,
                MAX(IF(DAY(tgl_presensi) = 5,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_5,
                MAX(IF(DAY(tgl_presensi) = 6,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_6,
                MAX(IF(DAY(tgl_presensi) = 7,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_7,
                MAX(IF(DAY(tgl_presensi) = 8,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_8,
                MAX(IF(DAY(tgl_presensi) = 9,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_9,
                MAX(IF(DAY(tgl_presensi) = 10,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_10,
                MAX(IF(DAY(tgl_presensi) = 11,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_11,
                MAX(IF(DAY(tgl_presensi) = 12,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_12,
                MAX(IF(DAY(tgl_presensi) = 13,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_13,
                MAX(IF(DAY(tgl_presensi) = 14,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_14,
                MAX(IF(DAY(tgl_presensi) = 15,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_15,
                MAX(IF(DAY(tgl_presensi) = 16,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_16,
                MAX(IF(DAY(tgl_presensi) = 17,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_17,
                MAX(IF(DAY(tgl_presensi) = 18,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_18,
                MAX(IF(DAY(tgl_presensi) = 19,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_19,
                MAX(IF(DAY(tgl_presensi) = 20,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_20,
                MAX(IF(DAY(tgl_presensi) = 21,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_21,
                MAX(IF(DAY(tgl_presensi) = 22,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_22,
                MAX(IF(DAY(tgl_presensi) = 23,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_23,
                MAX(IF(DAY(tgl_presensi) = 24,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_24,
                MAX(IF(DAY(tgl_presensi) = 25,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_25,
                MAX(IF(DAY(tgl_presensi) = 26,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_26,
                MAX(IF(DAY(tgl_presensi) = 27,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_27,
                MAX(IF(DAY(tgl_presensi) = 28,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_28,
                MAX(IF(DAY(tgl_presensi) = 29,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_29,
                MAX(IF(DAY(tgl_presensi) = 30,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_30,
                MAX(IF(DAY(tgl_presensi) = 31,CONCAT(status,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_31')
            ->join('users', 'presensi.users_id', '=', 'users.id')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->groupByRaw('presensi.users_id,name,npm')
            ->get();

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));
    }
}
