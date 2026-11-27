<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the healthcare facility (Faskes) that owns this request.
     */
    public function faskes(): BelongsTo
    {
        return $this->belongsTo(Faskes::class, 'faskes_id');
    }

    /**
     * Get the urgency level assigned to this request.
     */
    public function urgensi(): BelongsTo
    {
        return $this->belongsTo(Urgensi::class, 'urgensi_id');
    }

    /**
     * Get the user who created this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Determine whether this request can still be modified or deleted by the originating Faskes.
     *
     * Aturan bisnis sementara (provisional): hanya permintaan dengan status 'diajukan'
     * yang dapat diubah urgensi-nya atau dihapus.
     */
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }
}
