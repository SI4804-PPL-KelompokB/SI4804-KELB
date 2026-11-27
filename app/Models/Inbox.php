<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inbox extends Model
{
    use HasFactory;

    protected $table = 'inbox';

    protected $fillable = [
        'pengirim_id',
        'penerima_id',
        'subjek',
        'isi',
        'is_read',
        'read_at',
        'deleted_by_pengirim',
        'deleted_by_penerima',
    ];

    protected $casts = [
        'is_read'             => 'boolean',
        'read_at'             => 'datetime',
        'deleted_by_pengirim' => 'boolean',
        'deleted_by_penerima' => 'boolean',
    ];

    /**
     * Relasi ke pengguna pengirim pesan
     */
    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id')->with('faskes');
    }

    /**
     * Relasi ke pengguna penerima pesan
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id')->with('faskes');
    }

    /**
     * Scope untuk kotak masuk (inbox) milik pengguna tertentu
     */
    public function scopeInboxFor(Builder $query, int $userId): Builder
    {
        return $query->where('penerima_id', $userId)
            ->where('deleted_by_penerima', false);
    }

    /**
     * Scope untuk pesan terkirim (sent) milik pengguna tertentu
     */
    public function scopeSentBy(Builder $query, int $userId): Builder
    {
        return $query->where('pengirim_id', $userId)
            ->where('deleted_by_pengirim', false);
    }

    /**
     * Scope untuk pesan yang belum dibaca
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope pencarian berdasarkan kata kunci subjek, isi, atau nama pihak terkait
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('subjek', 'like', "%{$keyword}%")
              ->orWhere('isi', 'like', "%{$keyword}%")
              ->orWhereHas('pengirim', function (Builder $sq) use ($keyword) {
                  $sq->where('name', 'like', "%{$keyword}%");
              })
              ->orWhereHas('penerima', function (Builder $sq) use ($keyword) {
                  $sq->where('name', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Tandai pesan sebagai sudah dibaca
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Tandai pesan sebagai belum dibaca
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Hapus pesan secara aman untuk pengguna tertentu
     */
    public function deleteForUser(int $userId): bool
    {
        $updated = false;

        if ($this->penerima_id === $userId) {
            $this->deleted_by_penerima = true;
            $updated = true;
        }

        if ($this->pengirim_id === $userId) {
            $this->deleted_by_pengirim = true;
            $updated = true;
        }

        if (!$updated) {
            return false;
        }

        // Jika kedua belah pihak sudah menghapus pesan, hapus dari database secara permanen
        if ($this->deleted_by_pengirim && $this->deleted_by_penerima) {
            return (bool) $this->delete();
        }

        return $this->save();
    }
}
