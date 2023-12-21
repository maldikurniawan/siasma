<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                        echo "success|Terima Kasih, Sampai Jumpa|out";
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
                        echo "success|Terima Kasih, Selamat Belajar|in";
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

    public function izin(Request $request)
    {
        $id = Auth::guard()->user()->id;

        if (!empty($request->bulan) && !empty($request->tahun)) {
            $dataizin = DB::table('pengajuan_izin')
                ->where('users_id', $id)
                ->whereRaw('MONTH(tgl_izin)="' . $request->bulan . '"')
                ->whereRaw('YEAR(tgl_izin)="' . $request->tahun . '"')
                ->get();
        } else {
            $dataizin = DB::table('pengajuan_izin')
                ->where('users_id', $id)
                ->limit(5)
                ->orderBy('tgl_izin', 'desc')
                ->get();
        }

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.izin', compact('dataizin', 'namabulan'));
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

        $cekpresensi = DB::table('presensi')
            ->where('tgl_presensi', $tgl_izin)
            ->where('users_id', $id)
            ->count();

        if ($cekpresensi > 0) {
            return redirect('presensi/izin')->with(['error' => 'Anda Sudah Melakukan Presensi Pada Tanggal Tersebut']);
        } else {
            $simpan = DB::table('pengajuan_izin')->insert($data);

            if ($simpan) {
                return redirect('presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return redirect('presensi/izin')->with(['success' => 'Data Gagal Disimpan']);
            }
        }
    }

    public function editizin($kode_izin)
    {
        $dataizin = DB::table('pengajuan_izin')->where('id', $kode_izin)->first();
        return view('presensi.editizin', compact('dataizin'));
    }

    public function updateizin($kode_izin, Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        try {
            $data = [
                'tgl_izin' => $tgl_izin,
                'status' => $status,
                'keterangan' => $keterangan
            ];
            DB::table('pengajuan_izin')->where('id', $kode_izin)->update($data);
            return redirect('presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Throwable $e) {
            return redirect('presensi/izin')->with(['success' => 'Data Gagal Disimpan']);
        }
    }

    public function deleteizin($kode_izin)
    {
        try {
            DB::table('pengajuan_izin')->where('id', $kode_izin)->delete();
            return redirect('presensi/izin')->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Throwable $e) {
            return redirect('presensi/izin')->with(['success' => 'Data Gagal Dihapus']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $id = Auth::guard()->user()->id;

        $cek = DB::table('pengajuan_izin')->where('users_id', $id)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }

    public function showact($kode_izin)
    {
        $dataizin = DB::table('pengajuan_izin')->where('id', $kode_izin)->first();
        return view('presensi.showact', compact('dataizin'));
    }

    public function approveizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id = $request->id_izinsakit_form;
        $dataizin = DB::table('pengajuan_izin')->where('id', $id)->first();
        $users_id = $dataizin->users_id;
        $tgl_izin = $dataizin->tgl_izin;
        $status = $dataizin->status;
        $jam_absen = DB::table('jam_absen')->where('id', 1)->first();
        DB::beginTransaction();
        try {
            if ($status_approved == 1) {
                DB::table('presensi')->insert([
                    'users_id' => $users_id,
                    'tgl_presensi' => $tgl_izin,
                    'jam_absen_id' => $jam_absen->id,
                    'pengajuan_izin_id' => $id,
                    'jam_in' => "00:00:00",
                    'jam_out' => "00:00:00",
                    'status' => $status
                ]);
            }

            DB::table('pengajuan_izin')->where('id', $id)->update([
                'status_approved' => $status_approved
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function batalkanizinsakit($id)
    {
        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('id', $id)->update([
                'status_approved' => 0
            ]);
            DB::table('presensi')->where('pengajuan_izin_id', $id)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
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
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }

        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai = $rangetanggal[$lastrange];
        if ($jmlhari == 30) {
            array_push($rangetanggal, NULL);
        } else if ($jmlhari == 29) {
            array_push($rangetanggal, NULL, NULL);
        } else if ($jmlhari == 28) {
            array_push($rangetanggal, NULL, NULL, NULL);
        }

        $query = User::query();
        $query->selectRaw(
            "users.id, name, prodi, npm,
            tgl_1,
            tgl_2,
            tgl_3,
            tgl_4,
            tgl_5,
            tgl_6,
            tgl_7,
            tgl_8,
            tgl_9,
            tgl_10,
            tgl_11,
            tgl_12,
            tgl_13,
            tgl_14,
            tgl_15,
            tgl_16,
            tgl_17,
            tgl_18,
            tgl_19,
            tgl_20,
            tgl_21,
            tgl_22,
            tgl_23,
            tgl_24,
            tgl_25,
            tgl_26,
            tgl_27,
            tgl_28,
            tgl_29,
            tgl_30,
            tgl_31"
        );

        $query->leftJoin(
            DB::raw("(
                SELECT presensi.users_id,
                MAX(IF(tgl_presensi = '$rangetanggal[0]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_1,

                MAX(IF(tgl_presensi = '$rangetanggal[1]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_2,

                MAX(IF(tgl_presensi = '$rangetanggal[2]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_3,

                MAX(IF(tgl_presensi = '$rangetanggal[3]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_4,

                MAX(IF(tgl_presensi = '$rangetanggal[4]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_5,

                MAX(IF(tgl_presensi = '$rangetanggal[5]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_6,

                MAX(IF(tgl_presensi = '$rangetanggal[6]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_7,

                MAX(IF(tgl_presensi = '$rangetanggal[7]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_8,

                MAX(IF(tgl_presensi = '$rangetanggal[8]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_9,

                MAX(IF(tgl_presensi = '$rangetanggal[9]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_10,

                MAX(IF(tgl_presensi = '$rangetanggal[10]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_11,

                MAX(IF(tgl_presensi = '$rangetanggal[11]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_12,

                MAX(IF(tgl_presensi = '$rangetanggal[12]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_13,

                MAX(IF(tgl_presensi = '$rangetanggal[13]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_14,

                MAX(IF(tgl_presensi = '$rangetanggal[14]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_15,

                MAX(IF(tgl_presensi = '$rangetanggal[15]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_16,

                MAX(IF(tgl_presensi = '$rangetanggal[16]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_17,

                MAX(IF(tgl_presensi = '$rangetanggal[17]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_18,

                MAX(IF(tgl_presensi = '$rangetanggal[18]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_19,

                MAX(IF(tgl_presensi = '$rangetanggal[19]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_20,

                MAX(IF(tgl_presensi = '$rangetanggal[20]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_21,

                MAX(IF(tgl_presensi = '$rangetanggal[21]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_22,

                MAX(IF(tgl_presensi = '$rangetanggal[22]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_23,

                MAX(IF(tgl_presensi = '$rangetanggal[23]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_24,

                MAX(IF(tgl_presensi = '$rangetanggal[24]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_25,

                MAX(IF(tgl_presensi = '$rangetanggal[25]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_26,

                MAX(IF(tgl_presensi = '$rangetanggal[26]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_27,

                MAX(IF(tgl_presensi = '$rangetanggal[27]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_28,

                MAX(IF(tgl_presensi = '$rangetanggal[28]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_29,

                MAX(IF(tgl_presensi = '$rangetanggal[29]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_30,

                MAX(IF(tgl_presensi = '$rangetanggal[30]',
                CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(matkul,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.pengajuan_izin_id,'NA'),'|',
                IFNULL(keterangan,'NA'),'|'
                ),NULL)) as tgl_31

                FROM presensi
                LEFT JOIN jam_absen ON presensi.jam_absen_id = jam_absen.id
                LEFT JOIN pengajuan_izin ON presensi.pengajuan_izin_id = pengajuan_izin.id
                WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND '$sampai'
                GROUP BY users_id
                )presensi"),
            function ($join) {
                $join->on('users.id', '=', 'presensi.users_id');
            }
        );

        $query->orderBy('name');
        $rekap = $query->get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap', 'rangetanggal', 'jmlhari'));
    }
}
