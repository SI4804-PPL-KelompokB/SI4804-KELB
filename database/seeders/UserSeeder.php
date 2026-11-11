<?php

namespace Database\Seeders;

use App\Models\Faskes;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat master Faskes sesuai proposal & mockup
        $puskesmasSukamaju = Faskes::updateOrCreate(
            ['nama_faskes' => 'Puskesmas Sukamaju'],
            [
                'jenis' => 'Puskesmas',
                'alamat' => 'Jl. Sukamaju No. 18, Sukajadi',
                'kota' => 'Kota Bandung',
                'telepon' => '022-2031122',
            ]
        );

        $rsudBandung = Faskes::updateOrCreate(
            ['nama_faskes' => 'RSUD Kota Bandung'],
            [
                'jenis' => 'Rumah Sakit',
                'alamat' => 'Jl. Rumah Sakit No. 22, Ujungberung',
                'kota' => 'Kota Bandung',
                'telepon' => '022-7800017',
            ]
        );

        $gudangFarmasi = Faskes::updateOrCreate(
            ['nama_faskes' => 'Gudang Farmasi Dinkes Kota Bandung'],
            [
                'jenis' => 'Gudang Farmasi',
                'alamat' => 'Jl. Citarum No. 34, Cihapit, Bandung Wetan',
                'kota' => 'Kota Bandung',
                'telepon' => '022-4233211',
            ]
        );

        // 2. Akun Faskes (Ayu Nuraini - Puskesmas Sukamaju, persis sesuai Mockup 4.5 & 4.6)
        User::updateOrCreate(
            ['email' => 'ayu.nuraini@puskesmas.go.id'],
            [
                'name' => 'Ayu Nuraini',
                'password' => Hash::make('password123'),
                'role' => 'faskes',
                'jabatan' => 'Petugas Farmasi',
                'faskes_id' => $puskesmasSukamaju->id,
                'is_active' => true,
            ]
        );

        // 3. Akun Dinas Kesehatan (Dinkes)
        User::updateOrCreate(
            ['email' => 'dinkes@bandung.go.id'],
            [
                'name' => 'Dr. Hendra Gunawan',
                'password' => Hash::make('password123'),
                'role' => 'dinkes',
                'jabatan' => 'Koordinator Pengawasan & Distribusi Obat',
                'faskes_id' => null, // Sesuai relasi ERD: Dinkes tidak terikat faskes
                'is_active' => true,
            ]
        );

        // 4. Akun Penyedia (Gudang Farmasi / Mitra Penyuplai)
        User::updateOrCreate(
            ['email' => 'penyedia@farmasikota.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'penyedia',
                'jabatan' => 'Kepala Unit Logistik Farmasi',
                'faskes_id' => $gudangFarmasi->id,
                'is_active' => true,
            ]
        );

        // 5. Akun Administrator Sistem (Admin)
        User::updateOrCreate(
            ['email' => 'admin@medstock.id'],
            [
                'name' => 'Admin MedStock',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'jabatan' => 'Super Administrator Sistem',
                'faskes_id' => null, // Sesuai relasi ERD: Admin tidak terikat faskes
                'is_active' => true,
            ]
        );
    }
}
