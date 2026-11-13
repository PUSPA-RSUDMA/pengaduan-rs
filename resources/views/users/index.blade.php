@extends('layouts.admin')

@section('title', 'Management User & Hak Akses')

@section('content')

{{-- CSS KHUSUS --}}
<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

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
        background-color: #10b981;
        color: white; border: none; border-radius: 8px;
        padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }
    
    .btn-action-icon {
        width: 34px; height: 34px;
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
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
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

    /* Permission Matrix Table */
    .perm-table th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc !important;
        color: #475569;
        font-weight: 700;
    }
    .perm-group-row td {
        background-color: #f0fdf4 !important;
        color: #065f46;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.6rem 1rem !important;
    }
    .form-check-input.perm-checkbox {
        width: 1.15em;
        height: 1.15em;
        cursor: pointer;
        border-color: #cbd5e1;
    }
    .form-check-input.perm-checkbox:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
</style>

@php
    $actionLabels = [
        'view'   => ['label' => 'View',   'badge' => 'bg-info-subtle text-info border-info-subtle'],
        'create' => ['label' => 'Create', 'badge' => 'bg-success-subtle text-success border-success-subtle'],
        'edit'   => ['label' => 'Edit',   'badge' => 'bg-warning-subtle text-warning border-warning-subtle'],
        'delete' => ['label' => 'Delete', 'badge' => 'bg-danger-subtle text-danger border-danger-subtle'],
    ];

    $flatMenuLabels = [];
    foreach ($availableMenus as $grp => $items) {
        foreach ($items as $k => $m) {
            $flatMenuLabels[$k] = $m['label'];
        }
    }
@endphp

<div class="row justify-content-center">
    <div class="col-12">

        {{-- 1. NOTIFIKASI SUKSES (HIJAU) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 2. NOTIFIKASI ERROR (MERAH) --}}
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
                        <h5 class="text-title m-0"><i class="bi bi-people-fill text-primary me-2"></i>Management User & Hak Akses Menu</h5>
                        <small class="text-muted">Kelola akun pengguna, role jabatan, serta izin akses menu dan aksi (View, Create, Edit, Delete)</small>
                    </div>
                    @if(auth()->user()->hasPermission('users', 'create'))
                    <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
                    </button>
                    @endif
                </div>
                
                {{-- TABLE SECTION --}}
                <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
                    <table class="table table-modern align-middle table-hover m-0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="4%">No</th>
                                <th class="text-start" width="18%">Nama & Unit</th>
                                <th class="text-start" width="16%">Email Login</th>
                                <th width="13%">Role (Jabatan)</th>
                                <th class="text-start" width="33%">Hak Akses Menu & Action</th>
                                <th width="8%">Terdaftar</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        @if(auth()->user()->id == $user->id)
                                            <span class="badge bg-success" style="font-size: 0.65rem; border-radius: 12px; padding: 0.2rem 0.5rem;">Saya</span>
                                        @endif
                                    </div>
                                    @if($user->unit_name)
                                        <div class="small text-muted"><i class="bi bi-building me-1"></i>{{ $user->unit_name }}</div>
                                    @endif
                                </td>
                                <td class="text-start text-muted" style="font-size: 0.85rem;">{{ $user->email }}</td>
                                <td class="text-center">
                                    @if($user->role == 'admin')
                                        <span class="badge-soft badge-soft-primary"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                    @elseif($user->role == 'staff' || $user->role == 'relationship')
                                        <span class="badge-soft badge-soft-info"><i class="bi bi-briefcase-fill me-1"></i> Staff / Relationship</span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary"><i class="bi bi-person-fill me-1"></i> User Umum</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    @if($user->role === 'admin')
                                        <span class="badge-soft badge-soft-success">
                                            <i class="bi bi-check-all me-1 fs-6"></i> Full Akses (Semua Menu & Aksi)
                                        </span>
                                    @else
                                        @php
                                            $userMenuCount = 0;
                                            $activeMenusSummary = [];
                                            foreach ($flatMenuLabels as $mKey => $mLabel) {
                                                if ($user->hasPermission($mKey, 'view')) {
                                                    $userMenuCount++;
                                                    $acts = [];
                                                    foreach (['view', 'create', 'edit', 'delete'] as $actKey) {
                                                        if ($user->hasPermission($mKey, $actKey)) {
                                                            $acts[] = strtoupper(substr($actKey, 0, 1));
                                                        }
                                                    }
                                                    $activeMenusSummary[] = [
                                                        'label' => $mLabel,
                                                        'acts'  => implode('/', $acts),
                                                    ];
                                                }
                                            }
                                        @endphp

                                        @if($userMenuCount > 0)
                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                @foreach(array_slice($activeMenusSummary, 0, 4) as $sumItem)
                                                    <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.7rem;">
                                                        {{ Str::limit($sumItem['label'], 22) }}
                                                        <strong class="text-emerald ms-1" style="color:#059669;">[{{ $sumItem['acts'] }}]</strong>
                                                    </span>
                                                @endforeach
                                                @if(count($activeMenusSummary) > 4)
                                                    <span class="badge bg-secondary-subtle text-secondary fw-bold" style="font-size: 0.7rem;">
                                                        +{{ count($activeMenusSummary) - 4 }} menu lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge-soft badge-soft-warning">
                                                <i class="bi bi-exclamation-circle me-1"></i> Belum ada akses menu
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center text-muted" style="font-size: 0.8rem;">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @if(auth()->user()->hasPermission('users', 'edit'))
                                        {{-- Button Edit & Hak Akses --}}
                                        <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" title="Edit User & Hak Akses">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        @endif

                                        {{-- Button hapus (kecuali diri sendiri) --}}
                                        @if(auth()->user()->id != $user->id && auth()->user()->hasPermission('users', 'delete')) 
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user {{ $user->name }}? Akun tidak bisa dikembalikan.');" class="m-0">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus User">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal Edit User & Hak Akses --}}
                                    <div class="modal fade text-start" id="modalEdit{{ $user->id }}" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content shadow-lg">
                                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                                    <h6 class="modal-title-modern m-0">
                                                        <i class="bi bi-person-gear text-warning me-2"></i>Edit Pengguna & Hak Akses: <span class="text-primary">{{ $user->name }}</span>
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 bg-light" style="max-height: 78vh; overflow-y: auto;">
                                                        <div class="row g-4">
                                                            {{-- KOLOM KIRI: DATA AKUN --}}
                                                            <div class="col-lg-4">
                                                                <div class="card border-0 shadow-sm p-4 h-100">
                                                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-vcard text-primary me-2"></i>Informasi Akun</h6>
                                                                    
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
                                                                        <select name="role" class="form-select form-select-modern" onchange="togglePermissionBox(this.value, 'permBoxEdit{{ $user->id }}', 'adminNoticeEdit{{ $user->id }}')">
                                                                            <option value="user" {{ in_array($user->role, ['user', 'pengadu']) ? 'selected' : '' }}>User Umum</option>
                                                                            <option value="staff" {{ in_array($user->role, ['staff', 'relationship']) ? 'selected' : '' }}>Staff Unit</option>
                                                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator (Full Akses)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold text-muted small mb-1">Unit / Bagian (Opsional)</label>
                                                                        <select name="unit_name" class="form-select form-select-modern">
                                                                            <option value="">-- Tidak Spesifik / Umum --</option>
                                                                            @foreach($units as $u)
                                                                                <option value="{{ $u->name }}" {{ $user->unit_name == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    
                                                                    <hr class="text-muted opacity-25 my-3">
                                                                    
                                                                    <div class="mb-1">
                                                                        <label class="form-label fw-bold text-danger small mb-1"><i class="bi bi-key me-1"></i> Ganti Password (Opsional)</label>
                                                                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                                            <input type="password" name="password" class="form-control form-control-modern border-end-0" id="passEdit{{ $user->id }}" placeholder="Kosongkan jika tidak diganti...">
                                                                            <button class="btn btn-light border bg-white text-secondary px-3" type="button" onclick="togglePassword('passEdit{{ $user->id }}', this)" style="border-color: #e5e7eb !important;">
                                                                                <i class="bi bi-eye"></i>
                                                                            </button>
                                                                        </div>
                                                                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem">
                                                                            *Minimal 8 karakter jika ingin diganti.
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- KOLOM KANAN: MATRIKS HAK AKSES MENU & ACTION --}}
                                                            <div class="col-lg-8">
                                                                <div class="card border-0 shadow-sm p-4 h-100">
                                                                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3 gap-2">
                                                                        <div>
                                                                            <h6 class="fw-bold text-dark m-0"><i class="bi bi-shield-check text-success me-2"></i>Pengaturan Hak Akses Menu & Action</h6>
                                                                            <small class="text-muted" style="font-size: 0.75rem;">Centang menu dan aksi yang diizinkan untuk user ini</small>
                                                                        </div>
                                                                        <div class="d-flex gap-1" id="permBtnGroupEdit{{ $user->id }}">
                                                                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkAllPermissions('permBoxEdit{{ $user->id }}', true)">
                                                                                <i class="bi bi-check2-all"></i> Pilih Semua
                                                                            </button>
                                                                            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkActionOnly('permBoxEdit{{ $user->id }}', 'view')">
                                                                                <i class="bi bi-eye"></i> Hanya View
                                                                            </button>
                                                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkAllPermissions('permBoxEdit{{ $user->id }}', false)">
                                                                                <i class="bi bi-x-lg"></i> Reset
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Notice ketika Role = Admin --}}
                                                                    <div id="adminNoticeEdit{{ $user->id }}" class="alert alert-primary border-0 rounded-3 {{ $user->role === 'admin' ? '' : 'd-none' }}">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bi bi-shield-lock-fill fs-3 me-3 text-primary"></i>
                                                                            <div>
                                                                                <strong>Role Administrator Memiliki Full Akses Otomatis</strong>
                                                                                <div class="small">User dengan role <b>Administrator</b> secara otomatis dapat mengakses seluruh menu dan seluruh aksi (View, Create, Edit, Delete). Ubah role ke <b>Staff Unit</b> atau <b>User Umum</b> jika ingin membatasi menu secara kustom.</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Tabel Matriks Permission --}}
                                                                    <div id="permBoxEdit{{ $user->id }}" class="{{ $user->role === 'admin' ? 'd-none' : '' }}">
                                                                        <div class="table-responsive border rounded-3">
                                                                            <table class="table table-sm perm-table align-middle mb-0">
                                                                                <thead>
                                                                                    <tr class="text-center">
                                                                                        <th class="text-start ps-3" width="44%">Nama Menu / Modul</th>
                                                                                        <th width="14%">View<br><small class="fw-normal text-muted">(Lihat)</small></th>
                                                                                        <th width="14%">Create<br><small class="fw-normal text-muted">(Tambah)</small></th>
                                                                                        <th width="14%">Edit<br><small class="fw-normal text-muted">(Ubah)</small></th>
                                                                                        <th width="14%">Delete<br><small class="fw-normal text-muted">(Hapus)</small></th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($availableMenus as $groupName => $menus)
                                                                                        <tr class="perm-group-row">
                                                                                            <td colspan="5"><i class="bi bi-folder2-open me-1"></i> {{ $groupName }}</td>
                                                                                        </tr>
                                                                                        @foreach($menus as $menuKey => $menuMeta)
                                                                                            <tr>
                                                                                                <td class="ps-3 py-2">
                                                                                                    <div class="d-flex align-items-center justify-content-between pe-2">
                                                                                                        <div>
                                                                                                            <i class="bi {{ $menuMeta['icon'] }} text-secondary me-2"></i>
                                                                                                            <span class="fw-semibold text-dark" style="font-size: 0.82rem;">{{ $menuMeta['label'] }}</span>
                                                                                                        </div>
                                                                                                        @if(count($menuMeta['actions']) > 1)
                                                                                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" style="font-size: 0.68rem;" onclick="toggleRowPermissions(this)" title="Pilih/Hapus semua aksi di baris ini">
                                                                                                                Semua
                                                                                                            </button>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </td>
                                                                                                @foreach(['view', 'create', 'edit', 'delete'] as $act)
                                                                                                    <td class="text-center">
                                                                                                        @if(in_array($act, $menuMeta['actions']))
                                                                                                            <div class="form-check d-flex justify-content-center m-0">
                                                                                                                <input class="form-check-input perm-checkbox perm-act-{{ $act }}"
                                                                                                                       type="checkbox"
                                                                                                                       name="permissions[{{ $menuKey }}][]"
                                                                                                                       value="{{ $act }}"
                                                                                                                       data-menu="{{ $menuKey }}"
                                                                                                                       data-action="{{ $act }}"
                                                                                                                       onchange="handlePermChange(this, 'permBoxEdit{{ $user->id }}')"
                                                                                                                       {{ $user->hasPermission($menuKey, $act) ? 'checked' : '' }}>
                                                                                                            </div>
                                                                                                        @else
                                                                                                            <span class="text-muted opacity-25">—</span>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                @endforeach
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4" style="border-radius: 8px;">
                                                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                                                        </button>
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
                                <td colspan="7" class="text-center py-5">
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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-person-plus-fill text-primary me-2"></i>Buat Akun User Baru & Atur Hak Akses</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light" style="max-height: 78vh; overflow-y: auto;">
                    <div class="row g-4">
                        {{-- KOLOM KIRI: DATA AKUN --}}
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm p-4 h-100">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-vcard text-primary me-2"></i>Informasi Akun</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-modern" placeholder="Contoh: Budi Santoso" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small mb-1">Email Login <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-modern" placeholder="user@contoh.com" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small mb-1">Jabatan (Role) <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select form-select-modern" required onchange="togglePermissionBox(this.value, 'permBoxAdd', 'adminNoticeAdd')">
                                        <option value="staff" selected>Staff Unit (Kustom Hak Akses)</option>
                                        <option value="user">User Umum (Kustom Hak Akses)</option>
                                        <option value="admin">Administrator (Full Akses)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small mb-1">Unit / Bagian (Opsional)</label>
                                    <select name="unit_name" class="form-select form-select-modern">
                                        <option value="">-- Tidak Spesifik / Umum --</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->name }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-1">
                                    <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-key me-1"></i> Password Awal <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        <input type="password" name="password" class="form-control form-control-modern border-end-0" id="passAdd" placeholder="Masukkan password..." required>
                                        <button class="btn btn-light border bg-white text-secondary px-3" type="button" onclick="togglePassword('passAdd', this)" style="border-color: #e5e7eb !important;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem">
                                        <i class="bi bi-info-circle me-1"></i> Minimal 8 karakter.
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: MATRIKS HAK AKSES --}}
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm p-4 h-100">
                                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3 gap-2">
                                    <div>
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-shield-check text-success me-2"></i>Pengaturan Hak Akses Menu & Action</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">Pilih menu apa saja yang tampil dan aksi yang diperbolehkan</small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkAllPermissions('permBoxAdd', true)">
                                            <i class="bi bi-check2-all"></i> Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkActionOnly('permBoxAdd', 'view')">
                                            <i class="bi bi-eye"></i> Hanya View
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="checkAllPermissions('permBoxAdd', false)">
                                            <i class="bi bi-x-lg"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                {{-- Notice ketika Role = Admin --}}
                                <div id="adminNoticeAdd" class="alert alert-primary border-0 rounded-3 d-none">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-shield-lock-fill fs-3 me-3 text-primary"></i>
                                        <div>
                                            <strong>Role Administrator Memiliki Full Akses Otomatis</strong>
                                            <div class="small">User dengan role <b>Administrator</b> secara otomatis mendapatkan akses penuh ke seluruh menu dan aksi.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tabel Matriks Permission --}}
                                <div id="permBoxAdd">
                                    <div class="table-responsive border rounded-3">
                                        <table class="table table-sm perm-table align-middle mb-0">
                                            <thead>
                                                <tr class="text-center">
                                                    <th class="text-start ps-3" width="44%">Nama Menu / Modul</th>
                                                    <th width="14%">View<br><small class="fw-normal text-muted">(Lihat)</small></th>
                                                    <th width="14%">Create<br><small class="fw-normal text-muted">(Tambah)</small></th>
                                                    <th width="14%">Edit<br><small class="fw-normal text-muted">(Ubah)</small></th>
                                                    <th width="14%">Delete<br><small class="fw-normal text-muted">(Hapus)</small></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availableMenus as $groupName => $menus)
                                                    <tr class="perm-group-row">
                                                        <td colspan="5"><i class="bi bi-folder2-open me-1"></i> {{ $groupName }}</td>
                                                    </tr>
                                                    @foreach($menus as $menuKey => $menuMeta)
                                                        @php
                                                            $defaultCheckedMenus = ['complaints', 'permintaan', 'permohonan_informasi', 'permohonan_uhc', 'pkrs', 'logbook', 'bpjs'];
                                                            $isDefaultMenu = in_array($menuKey, $defaultCheckedMenus);
                                                        @endphp
                                                        <tr>
                                                            <td class="ps-3 py-2">
                                                                <div class="d-flex align-items-center justify-content-between pe-2">
                                                                    <div>
                                                                        <i class="bi {{ $menuMeta['icon'] }} text-secondary me-2"></i>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.82rem;">{{ $menuMeta['label'] }}</span>
                                                                    </div>
                                                                    @if(count($menuMeta['actions']) > 1)
                                                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" style="font-size: 0.68rem;" onclick="toggleRowPermissions(this)" title="Pilih/Hapus semua aksi di baris ini">
                                                                            Semua
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            @foreach(['view', 'create', 'edit', 'delete'] as $act)
                                                                <td class="text-center">
                                                                    @if(in_array($act, $menuMeta['actions']))
                                                                        <div class="form-check d-flex justify-content-center m-0">
                                                                            <input class="form-check-input perm-checkbox perm-act-{{ $act }}"
                                                                                   type="checkbox"
                                                                                   name="permissions[{{ $menuKey }}][]"
                                                                                   value="{{ $act }}"
                                                                                   data-menu="{{ $menuKey }}"
                                                                                   data-action="{{ $act }}"
                                                                                   onchange="handlePermChange(this, 'permBoxAdd')"
                                                                                   {{ ($isDefaultMenu && in_array($act, ['view', 'create', 'edit'])) ? 'checked' : '' }}>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted opacity-25">—</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Buat Akun & Simpan Hak Akses
                    </button>
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

    function togglePermissionBox(role, boxId, noticeId) {
        const box = document.getElementById(boxId);
        const notice = document.getElementById(noticeId);
        if (!box || !notice) return;

        if (role === 'admin') {
            box.classList.add('d-none');
            notice.classList.remove('d-none');
        } else {
            box.classList.remove('d-none');
            notice.classList.add('d-none');
        }
    }

    function checkAllPermissions(containerId, isChecked) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = isChecked;
        });
    }

    function checkActionOnly(containerId, actionName) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = (cb.getAttribute('data-action') === actionName);
        });
    }

    function toggleRowPermissions(btn) {
        const row = btn.closest('tr');
        if (!row) return;
        const checkboxes = row.querySelectorAll('.perm-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    // Otomatis centang 'view' jika 'create'/'edit'/'delete' dicentang;
    // dan otomatis hapus centang 'create'/'edit'/'delete' jika 'view' dilepas
    function handlePermChange(checkbox, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const menu = checkbox.getAttribute('data-menu');
        const action = checkbox.getAttribute('data-action');

        if (action !== 'view' && checkbox.checked) {
            const viewCb = container.querySelector(`.perm-checkbox[data-menu="${menu}"][data-action="view"]`);
            if (viewCb) viewCb.checked = true;
        } else if (action === 'view' && !checkbox.checked) {
            container.querySelectorAll(`.perm-checkbox[data-menu="${menu}"]`).forEach(cb => {
                if (cb.getAttribute('data-action') !== 'view') {
                    cb.checked = false;
                }
            });
        }
    }
</script>

@endsection
