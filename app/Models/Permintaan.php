<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';

    protected $table = 'permintaan';

    protected $fillable = [
        'faskes_id',
        'urgensi_id',
        'user_id',
        'status',
        'catatan',
    ];

    public function faskes(): BelongsTo
    {
        return $this->belongsTo(Faskes::class, 'faskes_id');
    }

    public function urgensi(): BelongsTo
    {
        return $this->belongsTo(Urgensi::class, 'urgensi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PermintaanItem::class, 'permintaan_id');
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }
}
