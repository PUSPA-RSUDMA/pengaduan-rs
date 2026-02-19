<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Source;
use App\Models\ReporterType;
use App\Models\UnitDestination;
use App\Models\Grade;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. SETUP AKUN PENGGUNA (ROLE)
        // ==========================================
        
        // A. Akun Admin (Super User - Bisa Segalanya)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'unit_name' => null
        ]);

        // B. Akun Staff Unit (Relationship - Cuma bisa Update & Lihat)
        User::create([
            'name' => 'Staff Unit',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'relationship',
            'unit_name' => 'Farmasi' // Default contoh: Staff Farmasi
        ]);

        // C. Akun User Umum (Pengadu - Cuma bisa Lapor)
        User::create([
            'name' => 'User Umum',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'pengadu',
            'unit_name' => null
        ]);


        // ==========================================
        // 2. SETUP DATA MASTER (SUPAYA TIDAK KOSONG)
        // ==========================================

        // A. MASTER MEDIA PENGADUAN (Source)
        $sources = [
            'WhatsApp',
            'Instagram',
            'Datang Langsung',
            'Tiktok',
            'Email Official',
            'Telepon RS',
            'Facebook'
        ];
        foreach ($sources as $s) {
            Source::create(['name' => $s]);
        }

        // B. MASTER UNIT PELAPOR (Siapa yang lapor?)
        $reporters = [
            'Pasien',
            'Keluarga Pasien',
            'Pengunjung',
            'Masyarakat Sekitar',
            'Karyawan/Internal',
            'LSM / Wartawan'
        ];
        foreach ($reporters as $r) {
            ReporterType::create(['name' => $r]);
        }

        // C. MASTER UNIT TUJUAN (Tujuan keluhan kemana?)
        $units = [
            'IGD',
            'Poli Rawat Jalan',
            'Rawat Inap',
            'Farmasi',
            'Kasir / Administrasi',
            'Laboratorium',
            'Radiologi',
            'Satpam / Keamanan',
            'Parkir & Kebersihan'
        ];
        foreach ($units as $u) {
            UnitDestination::create(['name' => $u]);
        }

        // D. MASTER KEGAWATAN (Grade)
        // Kita pakai kode HEX agar admin bisa tambah warna apa saja nanti.
        Grade::create([
            'name' => 'Tingkat Tinggi (Merah)', 
            'color_class' => '#dc3545', // Kode Merah
            'sla_hours' => 24
        ]);
        
        Grade::create([
            'name' => 'Tingkat Sedang (Kuning)', 
            'color_class' => '#ffc107', // Kode Kuning
            'sla_hours' => 48
        ]);
        
        Grade::create([
            'name' => 'Tingkat Rendah (Hijau)', 
            'color_class' => '#198754', // Kode Hijau
            'sla_hours' => 72
        ]);
        
    }
}