@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')

{{-- CSS KHUSUS: OBAT ANTI MATA GANDA --}}
<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }
</style>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna Sistem</h6>
            <small class="text-muted">Kelola akses login staf & admin</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah User
        </button>
    </div>
    
    <div class="card-body">
        
        {{-- 1. NOTIFIKASI SUKSES (HIJAU) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 2. NOTIFIKASI ERROR (MERAH) - INI YANG KEMARIN HILANG --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
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

        {{-- Tabel user --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email Login</th>
                        <th>Role (Jabatan)</th>
                        <th>Terdaftar Sejak</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="fw-bold">
                            {{ $user->name }}
                            @if(auth()->user()->id == $user->id)
                                <span class="badge bg-success ms-2">Saya</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->role == 'admin')
                                <span class="badge bg-primary">Administrator</span>
                            @elseif($user->role == 'staff')
                                <span class="badge bg-info text-dark">Staff Unit</span>
                            @else
                                <span class="badge bg-secondary">User Umum</span>
                            @endif
                        </td>
                        <td class="text-center small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                {{-- Button edit --}}
                                <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Button hapus (kecuali diri sendiri) --}}
                                @if(auth()->user()->id != $user->id) 
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini? Akun tidak bisa dikembalikan.');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" title="Hapus User"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>

                            {{-- Modal Edit User --}}
                            <div class="modal fade text-start" id="modalEdit{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title fw-bold">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Email Login</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Jabatan (Role)</label>
                                                    <select name="role" class="form-select">
                                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Umum</option>
                                                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff Unit</option>
                                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                                    </select>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-danger">Ganti Password (Opsional)</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password" class="form-control" id="passEdit{{ $user->id }}" placeholder="Kosongkan jika tidak diganti...">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passEdit{{ $user->id }}', this)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.75rem">
                                                        *Minimal 8 karakter jika ingin diganti.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning fw-bold">Update Data</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- End Modal Edit --}}

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah User --}}
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Buat Akun Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control" placeholder="user@contoh.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hak Akses</label>
                        <select name="role" class="form-select">
                            <option value="user">User Umum</option>
                            <option value="staff">Staff Unit</option>
                            <option value="admin">Administrator (Full Akses)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Awal</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passAdd" placeholder="Masukan password..." required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passAdd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.75rem">
                            <i class="bi bi-info-circle me-1"></i> Minimal 8 karakter.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Buat Akun</button>
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