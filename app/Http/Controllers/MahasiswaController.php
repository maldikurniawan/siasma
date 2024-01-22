<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $query->select('users.*');
        $query->orderBy('name');
        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $mahasiswa = $query->paginate(5);
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
            $foto = $npm . "." . $request->file('foto')->getClientOriginalExtension();
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

    public function edit(Request $request)
    {
        $id = $request->id;
        $users = DB::table('users')->where('id', $id)->first();
        return view('mahasiswa.edit', compact('users'));
    }

    public function update($id, Request $request)
    {
        $id = $request->id;
        $npm = $request->npm;
        $name = $request->name;
        $email = $request->email;
        $prodi = $request->prodi;
        $no_hp = $request->no_hp;
        $old_foto = $request->old_foto;
        if ($request->hasFile('foto')) {
            $foto = $npm . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data = [
                'npm' => $npm,
                'name' => $name,
                'email' => $email,
                'prodi' => $prodi,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
            $update = DB::table('users')->where('id', $id)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/mahasiswa/";
                    $folderPathOld = "public/uploads/mahasiswa/" . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            // dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($id)
    {
        $delete = DB::table('users')->where('id', $id)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
