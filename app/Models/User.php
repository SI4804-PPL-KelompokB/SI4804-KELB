<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'faskes_id',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke Fasilitas Kesehatan (Faskes)
     */
    public function faskes(): BelongsTo
    {
        return $this->belongsTo(Faskes::class, 'faskes_id');
    }

    /**
     * Memeriksa apakah pengguna memiliki peran tertentu
     * Sesuai rancangan Class Diagram subbab 4.4: +hasRole(r)
     */
    public function hasRole(string|array $roles): bool
    {
        $currentRole = strtolower($this->role ?? '');

        if (is_array($roles)) {
            $normalized = array_map(fn($r) => strtolower($r), $roles);
            return in_array($currentRole, $normalized, true);
        }

        return $currentRole === strtolower($roles);
    }

    /**
     * Label manusiawi untuk peran pengguna
     */
    public function getRoleLabelAttribute(): string
    {
        return match (strtolower($this->role ?? '')) {
            'admin' => 'Administrator Sistem',
            'dinkes' => 'Dinas Kesehatan',
            'faskes' => 'Fasilitas Kesehatan',
            'penyedia' => 'Penyedia Obat',
            default => ucfirst($this->role ?? 'Pengguna'),
        };
    }

    /**
     * Hook login sesuai Class Diagram (+login)
     */
    public function recordLogin(): void
    {
        $this->forceFill([
            'last_login_at' => now(),
        ])->saveQuietly();
    }
}
