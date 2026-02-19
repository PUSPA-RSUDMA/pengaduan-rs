<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // TAMPILKAN HALAMAN PROFIL
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // UPDATE DATA DIRI (NAMA & EMAIL)
    public function update(Request $request): RedirectResponse
    {
        // 1. CEK ROLE
        if ($request->user()->role !== 'admin') {
            abort(403, 'Maaf, hanya Admin yang boleh mengubah data profil.');
        }

        // 2. VALIDASI INPUT (DENGAN PESAN B.INDO)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
        ], [
            // KAMUS TERJEMAHAN
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid (harus ada @).',
            'email.unique' => 'Email ini sudah dipakai pengguna lain.',
        ]);

        // 3. SIMPAN PERUBAHAN
        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // UPDATE PASSWORD (INI YANG MASIH INGGRIS TADI, SAYA TERJEMAHKAN DISINI)
    public function updatePassword(Request $request): RedirectResponse
    {
        // 1. CEK ROLE
        if ($request->user()->role !== 'admin') {
            abort(403, 'Maaf, hanya Admin yang boleh mengubah password.');
        }

        // 2. VALIDASI (DENGAN PESAN B.INDO)
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            // KAMUS TERJEMAHAN (Perhatikan bagian ini)
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah. Coba ingat lagi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
        ]);

        // 3. UPDATE PASSWORD DI DATABASE
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    // HAPUS AKUN
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Maaf, hanya Admin yang boleh menghapus akun.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Untuk keamanan, masukkan password Anda.',
            'password.current_password' => 'Password salah, akun gagal dihapus.',
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}