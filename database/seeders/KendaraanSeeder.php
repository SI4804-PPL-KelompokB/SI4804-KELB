<?php

namespace Database\Seeders;

use App\Models\Faskes;
use App\Models\Kendaraan;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * PBI-14: Seed data contoh kendaraan/kurir internal
     */
    public function run(): void
    {
        $gudangFarmasi = Faskes::where('nama_faskes', 'Gudang Farmasi Dinkes Kota Bandung')->first();

        if (!$gudangFarmasi) {
            return;
        }

        $data = [
            [
                'faskes_id' => $gudangFarmasi->id,
                'nama_kurir' => 'Ahmad Suryadi',
                'jenis_kendaraan' => 'Ambulans',
                'plat_nomor' => 'D 1234 MED',
                'nomor_telepon' => '0812-3456-7890',
                'status' => 'aktif',
                'keterangan' => 'Ambulans utama untuk distribusi obat darurat wilayah Bandung Utara.',
            ],
            [
                'faskes_id' => $gudangFarmasi->id,
                'nama_kurir' => 'Rizki Firmansyah',
                'jenis_kendaraan' => 'Motor',
                'plat_nomor' => 'D 5678 OBT',
                'nomor_telepon' => '0813-9876-5432',
                'status' => 'aktif',
                'keterangan' => 'Kurir motor untuk pengiriman cepat obat ringan dan vaksin.',
            ],
            [
                'faskes_id' => $gudangFarmasi->id,
                'nama_kurir' => 'Dedi Kurniawan',
                'jenis_kendaraan' => 'Mobil Box',
                'plat_nomor' => 'D 9012 FAR',
                'nomor_telepon' => '0857-1234-5678',
                'status' => 'maintenance',
                'keterangan' => 'Mobil box untuk pengiriman stok obat dalam jumlah besar. Sedang servis rutin.',
            ],
            [
                'faskes_id' => $gudangFarmasi->id,
                'nama_kurir' => 'Hendra Pratama',
                'jenis_kendaraan' => 'Pick Up',
                'plat_nomor' => 'D 3456 STK',
                'nomor_telepon' => '0821-6543-2109',
                'status' => 'aktif',
                'keterangan' => 'Pick up cadangan untuk distribusi menengah.',
            ],
            [
                'faskes_id' => $gudangFarmasi->id,
                'nama_kurir' => 'Joko Widodo',
                'jenis_kendaraan' => 'Motor',
                'plat_nomor' => 'D 7890 EXP',
                'nomor_telepon' => null,
                'status' => 'nonaktif',
                'keterangan' => 'Kurir sudah tidak aktif bertugas.',
            ],
        ];

        foreach ($data as $kendaraan) {
            Kendaraan::updateOrCreate(
                ['plat_nomor' => $kendaraan['plat_nomor']],
                $kendaraan
            );
        }
    }
}
