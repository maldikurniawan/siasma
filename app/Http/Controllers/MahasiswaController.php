<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = DB::table('users')->orderBy('name')->get();
        return view('mahasiswa.index', compact('mahasiswa'));
    }
}
