<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unit_name', // <--- PENTING: Ini wajib ada supaya akun Relationship bisa disimpan
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];

    /**
     * Daftar seluruh modul menu dan aksi yang tersedia untuk pengaturan hak akses.
     */
    public static function availableMenus(): array
    {
        return [
            'Home & Dashboard' => [
                'utama_dashboard' => [
                    'label' => 'Dashboard Utama (Home)',
                    'icon'  => 'bi-house-door-fill',
                    'route' => 'utama.dashboard',
                    'actions' => ['view'],
                ],
            ],
            'Layanan Publik' => [
                'pengaduan_dashboard' => [
                    'label' => 'Dashboard Pengaduan',
                    'icon'  => 'bi-bar-chart-line-fill',
                    'route' => 'dashboard',
                    'actions' => ['view'],
                ],
                'complaints' => [
                    'label' => 'Data Keluhan / Pengaduan',
                    'icon'  => 'bi-chat-square-text',
                    'route' => 'complaints.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'permintaan_dashboard' => [
                    'label' => 'Dashboard Layanan Informasi',
                    'icon'  => 'bi-pie-chart-fill',
                    'route' => 'dashboard.permintaan',
                    'actions' => ['view'],
                ],
                'permintaan' => [
                    'label' => 'Data Permintaan Informasi',
                    'icon'  => 'bi-file-earmark-medical',
                    'route' => 'permintaan.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'permohonan_informasi' => [
                    'label' => 'Permohonan Informasi',
                    'icon'  => 'bi-envelope-paper',
                    'route' => 'permohonan-informasi.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'permohonan_uhc' => [
                    'label' => 'Peralihan UHC',
                    'icon'  => 'bi-hospital',
                    'route' => 'permohonan-uhc.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'lasehat_dashboard' => [
                    'label' => 'Dashboard Mobil LaSehat',
                    'icon'  => 'bi-speedometer2',
                    'route' => 'lasehat.dashboard',
                    'actions' => ['view'],
                ],
                'lasehat' => [
                    'label' => 'Data Mobil LaSehat',
                    'icon'  => 'bi-car-front',
                    'route' => 'lasehat.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            'Promosi Kesehatan (PKRS)' => [
                'pkrs_dashboard' => [
                    'label' => 'Dashboard PKRS',
                    'icon'  => 'bi-graph-up-arrow',
                    'route' => 'pkrs.dashboard',
                    'actions' => ['view'],
                ],
                'pkrs' => [
                    'label' => 'Kegiatan PKRS',
                    'icon'  => 'bi-megaphone',
                    'route' => 'pkrs.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            'Agenda & Integrasi' => [
                'logbook' => [
                    'label' => 'Kalender Logbook',
                    'icon'  => 'bi-calendar-event',
                    'route' => 'logbook.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'whatsapp' => [
                    'label' => 'Koneksi WhatsApp & Live Chat',
                    'icon'  => 'bi-whatsapp',
                    'route' => 'whatsapp.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'bpjs' => [
                    'label' => 'Cari Kepesertaan BPJS',
                    'icon'  => 'bi-credit-card-2-front',
                    'route' => 'bpjs.index',
                    'actions' => ['view', 'edit'],
                ],
            ],
            'Pengaturan & Data Master' => [
                'master' => [
                    'label' => 'Data Master (Unit, Media, Kategori, Supir, Grade, PKRS)',
                    'icon'  => 'bi-database',
                    'route' => 'master.units.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'users' => [
                    'label' => 'Management User & Hak Akses',
                    'icon'  => 'bi-people-fill',
                    'route' => 'users.index',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
            ],
        ];
    }

    /**
     * Cek apakah user memiliki izin (view, create, edit, delete) pada menu tertentu.
     */
    public function hasPermission(string $menu, string $action = 'view'): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $perms = $this->permissions;

        // Jika permissions sudah diatur secara spesifik oleh admin
        if (is_array($perms)) {
            if (!isset($perms[$menu]) || !is_array($perms[$menu])) {
                return false;
            }
            return in_array($action, $perms[$menu], true);
        }

        // Fallback default untuk akun lama yang belum diatur permissions-nya (null)
        $defaultMenus = [
            'complaints'           => ['view', 'create', 'edit'],
            'permintaan'           => ['view', 'create', 'edit', 'delete'],
            'permohonan_informasi' => ['view', 'create', 'edit', 'delete'],
            'permohonan_uhc'       => ['view', 'create', 'edit', 'delete'],
            'pkrs'                 => ['view', 'create', 'edit', 'delete'],
            'logbook'              => ['view', 'create', 'edit', 'delete'],
            'bpjs'                 => ['view', 'edit'],
        ];

        return isset($defaultMenus[$menu]) && in_array($action, $defaultMenus[$menu], true);
    }

    /**
     * Cek apakah user memiliki minimal satu akses menu di dalam daftar grup menu (untuk menampilkan parent menu di sidebar).
     */
    public function hasAnyMenuPermission(array $menus): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        foreach ($menus as $menu) {
            if ($this->hasPermission($menu, 'view')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dapatkan nama route halaman awal terbaik yang bisa diakses user setelah login.
     */
    public function firstAccessibleRoute(): string
    {
        if ($this->role === 'admin') {
            return 'utama.dashboard';
        }

        foreach (self::availableMenus() as $group => $items) {
            foreach ($items as $menuKey => $info) {
                if ($this->hasPermission($menuKey, 'view') && !empty($info['route'])) {
                    return $info['route'];
                }
            }
        }

        return 'profile.edit';
    }
}