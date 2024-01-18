<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = DB::table('users')->orderBy('name')->get();
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $npm = $request->npm;
        $name = $request->name;
        $email = $request->email;
        $prodi = $request->prodi;
        $no_hp = $request->no_hp;
        $password = Hash::make('test1234');
        // $users = DB::table('users')->where('id', $id)->first();
        if ($request->hasFile('foto')) {
            $foto = $id . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'npm' => $npm,
                'name' => $name,
                'email' => $email,
                'prodi' => $prodi,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
            $simpan = DB::table('users')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/mahasiswa/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            // dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
