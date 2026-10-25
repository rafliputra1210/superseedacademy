<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AsesorManagerController extends Controller
{
    // Menampilkan daftar akun asesor
    public function index()
    {
        $asesors = User::where('role', 'asesor')->latest()->get();
        return view('admin.asesor_manager.index', compact('asesors'));
    }

    // Menyimpan akun asesor baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'asesor',
        ]);

        return back()->with('success', 'Akun Asesor berhasil ditambahkan!');
    }

    // Menghapus akun asesor
    public function destroy(User $user)
    {
        if ($user->role === 'asesor') {
            $user->delete();
            return back()->with('success', 'Akun Asesor berhasil dihapus!');
        }
        return back()->with('error', 'Gagal menghapus akun!');
    }
}