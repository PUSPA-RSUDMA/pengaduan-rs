@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')

{{-- CSS KHUSUS: OBAT ANTI MATA GANDA --}}
<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    /* Global Styles matching the modern dashboard reference */
    body {
        background-color: #f4f7f6;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .card { 
        border-radius: 16px !important; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; 
        border: 1px solid rgba(0,0,0,0.02) !important;
        background-color: #ffffff;
    }

    .text-title { color: #111827; font-weight: 700; font-size: 1.15rem; }
    
    /* Modern Button Styling */
    .btn-modern-primary {
        background-color: #10b981; /* Emerald green */
        color: white; border: none; border-radius: 8px;
        padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }
    
    .btn-action-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: none; transition: 0.2s;
    }
    .btn-action-icon:hover { transform: scale(1.05); }

    /* Modern Table Styling */
    .table-modern { margin-bottom: 0; }
    .table-modern > :not(caption) > * > * {
        padding: 1rem 1rem; border-bottom-color: #f3f4f6;
        vertical-align: middle; background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase; font-size: 0.75rem; font-weight: 600;
        color: #6b7280; border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }

    /* Soft Badges */
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

    /* Form & Modal Styling */
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    
    .modal-content { border-radius: 16px; border: none; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; border-radius: 16px 16px 0 0; }
    .modal-title-modern { font-weight: 700; color: #111827; font-size: 1.1rem; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; border-radius: 0 0 16px 16px; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-12 col-xl-11">

        {{-- 1. NOTIFIKASI SUKSES (HIJAU) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 2. NOTIFIKASI ERROR (MERAH) - INI YANG KEMARIN HILANG --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
                    <div>
                        <strong>Gagal Menyimpan!</strong> Periksa inputan Anda:
                        <ul class="mb-0 mt-1 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 mb-4">
            <div class="card-body p-4">
                
                {{-- HEADER SECTION --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h5 class="text-title m-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pengguna Sistem</h5>
                        <small class="text-muted">Kelola hak akses login untuk Administrator, Staff, dan User Umum</small>
                    </div>
                    <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
                    </button>
                </div>
                
                {{-- TABLE SECTION --}}
                <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
                    <table class="table table-modern align-middle table-hover m-0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th class="text-start">Nama Lengkap</th>
                                <th class="text-start">Email Login</th>
                                <th>Role (Jabatan)</th>
                                <th>Terdaftar Sejak</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        {{ $user->name }}
                                        @if(auth()->user()->id == $user->id)
                                            <span class="badge bg-success" style="font-size: 0.65rem; border-radius: 12px; padding: 0.2rem 0.5rem;">Saya</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-start text-muted" style="font-size: 0.85rem;">{{ $user->email }}</td>
                                <td class="text-center">
                                    @if($user->role == 'admin')
                                        <span class="badge-soft badge-soft-primary"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                    @elseif($user->role == 'staff')
                                        <span class="badge-soft badge-soft-info"><i class="bi bi-briefcase-fill me-1"></i> Staff Unit</span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary"><i class="bi bi-person-fill me-1"></i> User Umum</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted" style="font-size: 0.85rem;">{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Button edit --}}
                                        <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" title="Edit User">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>

                                        {{-- Button hapus (kecuali diri sendiri) --}}
                                        @if(auth()->user()->id != $user->id) 
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini? Akun tidak bisa dikembalikan.');" class="m-0">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus User">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal Edit User --}}
                                    <div class="modal fade text-start" id="modalEdit{{ $user->id }}" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content shadow-lg">
                                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Pengguna</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 bg-light">
                                                        <div class="card border-0 shadow-sm p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-muted small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control form-control-modern" value="{{ $user->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-muted small mb-1">Email Login <span class="text-danger">*</span></label>
                                                                <input type="email" name="email" class="form-control form-control-modern" value="{{ $user->email }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-muted small mb-1">Jabatan (Role) <span class="text-danger">*</span></label>
                                                                <select name="role" class="form-select form-select-modern">
                                                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Umum</option>
                                                                    <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff Unit</option>
                                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <hr class="text-muted opacity-25 my-4">
                                                            
                                                            <div class="mb-1">
                                                                <label class="form-label fw-bold text-danger small mb-1"><i class="bi bi-key me-1"></i> Ganti Password (Opsional)</label>
                                                                <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                                    <input type="password" name="password" class="form-control form-control-modern border-end-0" id="passEdit{{ $user->id }}" placeholder="Kosongkan jika tidak diganti...">
                                                                    <button class="btn btn-light border bg-white text-secondary px-3" type="button" onclick="togglePassword('passEdit{{ $user->id }}', this)" style="border-color: #e5e7eb !important;">
                                                                        <i class="bi bi-eye"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem">
                                                                    *Minimal 8 karakter jika ingin diganti.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4" style="border-radius: 8px;">Update Data</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Modal Edit --}}

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-people fs-1 text-light mb-2"></i>
                                        <span>Data pengguna masih kosong.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah User Baru --}}
<div class="modal fade" id="modalAddUser" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-person-plus-fill text-primary me-2"></i>Buat Akun Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-modern" placeholder="Contoh: Budi Santoso" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-modern" placeholder="user@contoh.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Hak Akses (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-modern" required>
                                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                <option value="user">User Umum</option>
                                <option value="staff">Staff Unit</option>
                                <option value="admin">Administrator (Full Akses)</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-key me-1"></i> Password Awal <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <input type="password" name="password" class="form-control form-control-modern border-end-0" id="passAdd" placeholder="Masukan password..." required>
                                <button class="btn btn-light border bg-white text-secondary px-3" type="button" onclick="togglePassword('passAdd', this)" style="border-color: #e5e7eb !important;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem">
                                <i class="bi bi-info-circle me-1"></i> Minimal 8 karakter.
                            </small>
                        </div>

                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">Buat Akun</button>
                </div>
            </form>
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