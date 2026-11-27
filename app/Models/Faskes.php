<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faskes extends Model
{
    use HasFactory;

    protected $table = 'faskes';

    protected $fillable = [
        'nama_faskes',
        'jenis',
        'alamat',
        'kota',
        'telepon',
    ];

    /**
     * Get the users associated with this faskes.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'faskes_id');
    }

    /**
     * Get the emergency requests submitted by this faskes.
     */
    public function permintaan(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'faskes_id');
    }

    /**
     * PBI-14: Get the kendaraan/kurir associated with this faskes.
     */
    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'faskes_id');
    }
}
