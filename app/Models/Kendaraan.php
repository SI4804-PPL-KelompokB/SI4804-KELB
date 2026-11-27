<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'faskes_id',
        'nama_kurir',
        'jenis_kendaraan',
        'plat_nomor',
        'nomor_telepon',
        'status',
        'keterangan',
    ];

    /**
     * Jenis kendaraan yang tersedia
     */
    public const JENIS_KENDARAAN = [
        'Ambulans',
        'Motor',
        'Mobil Box',
        'Pick Up',
    ];

    /**
     * Status kendaraan yang tersedia
     */
    public const STATUS = [
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
        'maintenance' => 'Maintenance',
    ];

    /**
     * Relasi ke Fasilitas Kesehatan (Faskes) pemilik kendaraan
     */
    public function faskes(): BelongsTo
    {
        return $this->belongsTo(Faskes::class, 'faskes_id');
    }

    /**
     * Label status yang berwarna untuk badge UI
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Warna badge status untuk UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'emerald',
            'nonaktif' => 'red',
            'maintenance' => 'amber',
            default => 'slate',
        };
    }
}
