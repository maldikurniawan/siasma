<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/panel/homeadmin');
        } else {
            return redirect('/panel')->with(['warning'=>'Email atau Password Salah']);
        }
    }

    public function proseslogout(){
        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
            return redirect('/panel');
        }
    }
}
