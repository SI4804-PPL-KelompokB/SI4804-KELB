<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Eloquent minimal untuk Master Tingkat Urgensi (Shared Contract PBI-07 & PBI-11).
 *
 * CATATAN INTEGRASI PBI-11:
 * PBI-11 mewarisi model ini dan dapat menambahkan scope, accessor, atau proteksi
 * penghapusan (`isReferenced()`) tanpa membuat file model duplikat.
 */
class Urgensi extends Model
{
    use HasFactory;

    protected $table = 'urgensi';

    protected $fillable = [
        'nama_level',
        'batas_waktu_jam',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu_jam' => 'integer',
        ];
    }

    /**
     * Get the emergency requests associated with this urgency level.
     */
    public function permintaan(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'urgensi_id');
    }
}
