<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penawaran extends Model
{
    use HasFactory;

    public const STATUS_TERSEDIA = 'tersedia';

    protected $table = 'penawaran';

    protected $fillable = [
        'faskes_id',
        'user_id',
        'judul',
        'status',
        'catatan',
    ];

    public function faskes(): BelongsTo
    {
        return $this->belongsTo(Faskes::class, 'faskes_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_TERSEDIA;
    }
}
