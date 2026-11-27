<?php

namespace Database\Seeders;

use App\Models\Inbox;
use App\Models\User;
use Illuminate\Database\Seeder;

class InboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * PBI-21: Data awal pesan internal (Inbox)
     */
    public function run(): void
    {
        $ayu = User::where('email', 'ayu.nuraini@puskesmas.go.id')->first();
        $dinkes = User::where('email', 'dinkes@bandung.go.id')->first();
        $budi = User::where('email', 'penyedia@farmasikota.id')->first();
        $admin = User::where('email', 'admin@medstock.id')->first();

        if (! $ayu || ! $dinkes || ! $budi || ! $admin) {
            return;
        }

        // 1. Pesan belum dibaca dari Dinkes ke Ayu (Faskes Sukamaju)
        Inbox::updateOrCreate(
            [
                'pengirim_id' => $dinkes->id,
                'penerima_id' => $ayu->id,
                'subjek' => 'Pemberitahuan: Alokasi Tambahan Serum Anti Bisa Ular (ABU)',
            ],
            [
                'isi' => "Selamat pagi rekan farmasi Puskesmas Sukamaju.\n\nKami informasikan bahwa Dinas Kesehatan Kota Bandung telah mengalokasikan 10 vial serum antivenom (ABU) tambahan untuk wilayah Sukajadi. Mohon segera pastikan kesiapan tempat penyimpanan dingin (cold chain 2-8°C) di instalasi farmasi Anda sebelum pengiriman dijadwalkan.\n\nSalam,\nDr. Hendra Gunawan - Dinkes Kota Bandung",
                'is_read' => false,
                'read_at' => null,
                'deleted_by_pengirim' => false,
                'deleted_by_penerima' => false,
                'created_at' => now()->subHours(2),
            ]
        );

        // 2. Pesan sudah dibaca dari Budi (Gudang Farmasi) ke Ayu
        Inbox::updateOrCreate(
            [
                'pengirim_id' => $budi->id,
                'penerima_id' => $ayu->id,
                'subjek' => 'Konfirmasi Ketersediaan Cairan Infus Ringer Lactate 500 mL',
            ],
            [
                'isi' => "Yth. Ibu Ayu Nuraini,\n\nMenindaklanjuti permohonan informasi ketersediaan cairan infus Ringer Lactate 500 mL, gudang kami memiliki stok surplus sebanyak 120 botol dengan tanggal kedaluwarsa Agustus 2027. Silakan ajukan form permintaan darurat melalui sistem agar dapat segera kami siapkan surat jalannya.\n\nTerima kasih,\nBudi Santoso - Gudang Farmasi Kota Bandung",
                'is_read' => true,
                'read_at' => now()->subDay(),
                'deleted_by_pengirim' => false,
                'deleted_by_penerima' => false,
                'created_at' => now()->subDays(1)->subHours(3),
            ]
        );

        // 3. Pesan dari Ayu ke Dinkes
        Inbox::updateOrCreate(
            [
                'pengirim_id' => $ayu->id,
                'penerima_id' => $dinkes->id,
                'subjek' => 'Laporan Lonjakan Kebutuhan Insulin Glargine di Faskes',
            ],
            [
                'isi' => "Yth. Koordinator Distribusi Dinkes Kota Bandung,\n\nTerdapat peningkatan pasien rujukan diabetes darurat di Puskesmas Sukamaju selama dua hari terakhir sehingga stok insulin reguler dan glargine menipis di bawah batas aman (sisa 3 pen). Kami mohon rekomendasi redistribusi dari fasilitas terdekat. Terima kasih.\n\nHormat kami,\nAyu Nuraini, S.Farm.",
                'is_read' => false,
                'read_at' => null,
                'deleted_by_pengirim' => false,
                'deleted_by_penerima' => false,
                'created_at' => now()->subHours(5),
            ]
        );

        // 4. Pesan pengumuman sistem dari Admin ke Ayu
        Inbox::updateOrCreate(
            [
                'pengirim_id' => $admin->id,
                'penerima_id' => $ayu->id,
                'subjek' => 'Informasi Pemeliharaan Rutin Server MedStock.id',
            ],
            [
                'isi' => "Yth. Pengguna MedStock.id,\n\nKami menginformasikan bahwa akan dilaksanakan pemeliharaan sistem berkala pada hari Minggu pukul 01.00 - 03.00 WIB. Selama proses ini berlangsung, sistem mungkin mengalami jeda singkat. Layanan pelaporan darurat tetap dapat digunakan seperti biasa di luar jadwal pemeliharaan tersebut.\n\nTim Administrator MedStock.id",
                'is_read' => true,
                'read_at' => now()->subDays(2),
                'deleted_by_pengirim' => false,
                'deleted_by_penerima' => false,
                'created_at' => now()->subDays(2)->subHours(4),
            ]
        );
    }
}
