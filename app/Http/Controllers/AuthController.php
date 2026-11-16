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
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'cover_image' => 'required|image|max:5120',
            'certificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['owner_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'depot'
        ]);

        $certPath = $request->file('certificate')->store('certificates', 'public');
        $storePhotoPath = $request->file('cover_image')->store('depots', 'public');

        $depot = \App\Models\Depot::create([
            'user_id' => $user->id,
            'slug' => \Illuminate\Support\Str::slug($data['depot_name']) . '-' . uniqid(),
            'name' => $data['depot_name'],
            'tagline' => 'Depot Air Minum Isi Ulang Higienis Surabaya',
            'district' => $data['district'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'whatsapp' => preg_replace('/[^0-9]/', '', $data['phone']),
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lng'],
            'cover_image' => '/storage/' . $storePhotoPath,
            'certificate_document_path' => '/storage/' . $certPath,
            'is_certified' => false,
            'is_approved' => false,
            'certification_status' => 'BELUM_TERSERTIFIKASI',
            'open_hours' => '08:00 - 20:00 WIB • Setiap Hari',
            'is_open' => true,
            'facilities' => [
                'Filtrasi Multi-Stage & Sinar UV',
                'Pencucian Galon Higienis',
            ],
        ]);

        // Buat 2 produk air default agar pelanggan bisa langsung memesan setelah di-ACC Dinkes
        \App\Models\WaterProduct::create([
            'depot_id' => $depot->id,
            'name' => 'Air Mineral Standar',
            'type' => 'mineral',
            'price' => 6000,
            'description' => 'Air mineral pegunungan steril melalui filtrasi mikro & sinar UV.',
            'tds_avg' => 30,
        ]);

        \App\Models\WaterProduct::create([
            'depot_id' => $depot->id,
            'name' => 'Air RO Ultra-Pure',
            'type' => 'ro',
            'price' => 8000,
            'description' => 'Air murni Reverse Osmosis (RO) membran 0.0001 mikron dengan TDS ekstra rendah.',
            'tds_avg' => 15,
        ]);

        Auth::login($user);
        return redirect('/depot')->with('success', 'Pendaftaran depot berhasil dikirim! Menunggu ACC & verifikasi berkas dari Dinas Kesehatan sebelum tampil ke pelanggan.');
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
