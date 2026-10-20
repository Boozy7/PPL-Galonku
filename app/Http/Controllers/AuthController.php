<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showUserLogin()
    {
        return view('auth.login', ['type' => 'user']);
    }

    public function showDepotLogin()
    {
        return view('auth.login', ['type' => 'depot']);
    }

    public function showDinkesLogin()
    {
        return view('auth.login', ['type' => 'dinkes']);
    }

    public function showUserRegister()
    {
        return view('auth.register');
    }

    public function registerUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'user'
        ]);

        Auth::login($user);
        return redirect('/');
    }

    public function showDepotRegister()
    {
        return view('auth.register_depot');
    }

    public function registerDepot(Request $request)
    {
        $data = $request->validate([
            'owner_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',

            'depot_name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'certificate' => 'required|image|max:5120',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['owner_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'depot'
        ]);

        $path = $request->file('certificate')->store('certificates', 'public');

        \App\Models\Depot::create([
            'user_id' => $user->id,
            'slug' => \Illuminate\Support\Str::slug($data['depot_name']) . '-' . uniqid(),
            'name' => $data['depot_name'],
            'district' => $data['district'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'whatsapp' => preg_replace('/[^0-9]/', '', $data['phone']),
            'lat' => -7.250445,
            'lng' => 112.768845,
            'is_certified' => false,
            'certification_status' => 'BELUM_TERSERTIFIKASI'
        ]);

        Auth::login($user);
        return redirect('/depot')->with('success', 'Pendaftaran berhasil. Silakan tunggu verifikasi Dinkes atas sertifikat Anda.');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'type' => ['required', 'in:user,depot,dinkes']
        ]);

        // Require role to match the login portal type
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role' => $credentials['type']])) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'dinkes') {
                return redirect()->intended('/dinkes');
            } elseif ($user->role === 'depot') {
                return redirect()->intended('/depot');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah untuk akses portal ini.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
