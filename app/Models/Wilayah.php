<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama_wilayah',
        'deskripsi',
    ];

    /**
     * Relasi ke Fasilitas Kesehatan (Faskes).
     * FK wilayah_id akan ditambahkan ke tabel faskes pada PBI-02.
     * Method ini disiapkan agar PBI-02 cukup menambahkan kolom FK tanpa mengubah model ini.
     *
     * @see PBI-02: Manajemen Profil Fasilitas Kesehatan
     */
    // public function faskes(): HasMany
    // {
    //     return $this->hasMany(Faskes::class, 'wilayah_id');
    // }

    /**
     * Memeriksa apakah wilayah masih direferensikan oleh entitas lain.
     * Digunakan untuk proteksi hapus (pencegahan orphan deletion).
     * Setelah PBI-02 menambahkan FK, kondisi ini akan berfungsi via relasi.
     */
    public function isReferenced(): bool
    {
        // Setelah PBI-02: return $this->faskes()->exists();
        // Saat ini tabel faskes belum memiliki wilayah_id, jadi selalu false.
        return false;
    }
}
