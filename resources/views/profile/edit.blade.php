@extends('layouts.admin')

@section('title', 'Pengaturan Profil')

@section('content')

{{-- CSS KHUSUS: Hilangkan mata bawaan browser --}}
<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }
</style>

{{-- ============================================================== --}}
{{-- BAGIAN NOTIFIKASI --}}
{{-- ============================================================== --}}

{{-- 1. SUKSES (HIJAU) --}}
@if (session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i><strong>Berhasil!</strong> Data profil Anda telah diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@elseif (session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
        <i class="bi bi-key-fill me-2"></i><strong>Berhasil!</strong> Password Anda telah diganti. Silakan ingat password baru Anda.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- 2. ERROR / GAGAL (MERAH) --}}
@if ($errors->any() || $errors->updatePassword->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong>Gagal Menyimpan!</strong> Silakan perbaiki kesalahan berikut:
                <ul class="mb-0 mt-1 small">
                    {{-- Error Profil --}}
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    
                    {{-- Error Password --}}
                    @foreach ($errors->updatePassword->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- PESAN MODE TERBATAS --}}
@if(auth()->user()->role !== 'admin')
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="bi bi-lock-fill me-2"></i>
        <strong>Mode Terbatas:</strong> Akun Staf/User hanya dapat melihat data. Hubungi Administrator jika ingin mengubah data.
    </div>
@endif

<div class="row">
    
    {{-- KOLOM KIRI: INFORMASI PROFIL --}}
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-person-bounding-box me-2"></i>Informasi Profil</h6>
            </div>
            <div class="card-body">
                
                {{-- FORM PROFIL --}}
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required 
                            {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required 
                            {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-4 text-end">
                        @if(auth()->user()->role === 'admin')
                            <button type="submit" class="btn btn-primary fw-bold px-4 btn-sm">
                                <i class="bi bi-save me-1"></i> SIMPAN PROFIL
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                                <i class="bi bi-lock-fill me-1"></i> Terkunci
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: GANTI PASSWORD --}}
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 font-weight-bold text-danger"><i class="bi bi-shield-lock-fill me-2"></i>Ganti Password</h6>
            </div>
            <div class="card-body">

                {{-- PERHATIAN: Di baris inilah kuncinya! --}}
                {{-- Kita arahkan ke 'profile.password.update' (Pintu Baru Bahasa Indonesia) --}}
                <form method="post" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('put')

                    {{-- PASSWORD LAMA --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Password Saat Ini</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" id="current_password" placeholder="Masukan password lama..." required {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                            
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            @if($errors->updatePassword->has('current_password'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->updatePassword->first('current_password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- PASSWORD BARU --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" id="password" placeholder="Minimal 8 karakter..." required {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                            
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            @if($errors->updatePassword->has('password'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->updatePassword->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ULANGI PASSWORD BARU --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Ulangi Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" id="password_confirmation" placeholder="Ketik ulang password baru..." required {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                            
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            @if($errors->updatePassword->has('password_confirmation'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->updatePassword->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        @if(auth()->user()->role === 'admin')
                            <button type="submit" class="btn btn-danger fw-bold px-4 btn-sm">
                                <i class="bi bi-key me-1"></i> UPDATE PASSWORD
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                                <i class="bi bi-lock-fill me-1"></i> Terkunci
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script>
    function togglePassword(inputId, btn) {
        let input = document.getElementById(inputId);
        let icon = btn.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>

@endsection