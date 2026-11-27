<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanItem extends Model
{
    use HasFactory;

    protected $table = 'permintaan_items';

    protected $fillable = [
        'permintaan_id',
        'nama_obat',
        'satuan',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'permintaan_id' => 'integer',
            'qty' => 'integer',
        ];
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }
}
