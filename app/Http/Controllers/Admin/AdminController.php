<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        return view('admin.index');
    }

    public function login() {
        return view('admin.login');
    }

    public function check_login(Request $req) {
        $req->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $data = $req->only('email','password');

        $check = auth()->attempt($data);

        if ($check) {
            return redirect()->route('admin.index')->with('ok','Welcom Back');
        }

        return redirect()->back()->with('no','Your email Or Password is not match');
    }

    public function logout() {
        auth()->logout();
        return redirect()->route('admin.login')->with('no','Logouted');
    }
}
