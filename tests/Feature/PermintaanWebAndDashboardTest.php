<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Urgensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermintaanWebAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $dinkes;

    private User $faskes;

    private User $penyedia;

    private Faskes $faskesEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faskesEntity = Faskes::create([
            'nama_faskes' => 'Puskesmas Sukamaju',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Sukamaju No. 18',
            'kota' => 'Kota Bandung',
        ]);

        $this->admin = User::create([
            'name' => 'Admin MedStock',
            'email' => 'admin@medstock.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->dinkes = User::create([
            'name' => 'Dr. Hendra Gunawan',
            'email' => 'dinkes@bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'dinkes',
            'is_active' => true,
        ]);

        $this->faskes = User::create([
            'name' => 'Ayu Nuraini',
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $this->faskesEntity->id,
            'is_active' => true,
        ]);

        $this->penyedia = User::create([
            'name' => 'Budi Santoso',
            'email' => 'penyedia@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'faskes_id' => $this->faskesEntity->id,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function role_dashboards_render_sidebar_and_role_appropriate_navigation(): void
    {
        // Admin: Dashboard + Manajemen Wilayah (no Permintaan Darurat)
        $this->actingAs($this->admin)->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('id="app-sidebar"', false)
            ->assertSee('Manajemen Wilayah')
            ->assertDontSee('Permintaan Darurat');

        // Dinkes: Dashboard + Manajemen Wilayah (no Permintaan Darurat)
        $this->actingAs($this->dinkes)->get('/dinkes/dashboard')
            ->assertStatus(200)
            ->assertSee('id="app-sidebar"', false)
            ->assertSee('Manajemen Wilayah')
            ->assertDontSee('Permintaan Darurat');

        // Faskes: Dashboard + Manajemen Wilayah + Permintaan Darurat
        $this->actingAs($this->faskes)->get('/faskes/dashboard')
            ->assertStatus(200)
            ->assertSee('id="app-sidebar"', false)
            ->assertSee('Manajemen Wilayah')
            ->assertSee('Permintaan Darurat');

        // Penyedia: Dashboard + Manajemen Wilayah (no Permintaan Darurat)
        $this->actingAs($this->penyedia)->get('/penyedia/dashboard')
            ->assertStatus(200)
            ->assertSee('id="app-sidebar"', false)
            ->assertSee('Manajemen Wilayah')
            ->assertDontSee('Permintaan Darurat');
    }

    #[Test]
    public function permintaan_web_page_enforces_role_access_and_handles_empty_urgensi_state(): void
    {
        // Unauthenticated redirected to login
        $this->get('/permintaan')->assertRedirect('/login');

        // Penyedia redirected away to own dashboard by web RoleMiddleware
        $this->actingAs($this->penyedia)->get('/permintaan')
            ->assertRedirect('/penyedia/dashboard')
            ->assertSessionHas('error');

        // Faskes with empty urgensi table sees warning and disabled submit state
        $this->actingAs($this->faskes)->get('/permintaan')
            ->assertStatus(200)
            ->assertSee('Permintaan Darurat')
            ->assertSee('Master Tingkat Urgensi Belum Tersedia')
            ->assertSee('id="btn-open-modal-create"', false);

        // When an urgensi level exists, option is rendered in selector
        Urgensi::create([
            'nama_level' => 'Prioritas Tinggi',
            'batas_waktu_jam' => 2,
        ]);

        $this->actingAs($this->faskes)->get('/permintaan')
            ->assertStatus(200)
            ->assertSee('Prioritas Tinggi')
            ->assertDontSee('Master Tingkat Urgensi Belum Tersedia');
    }
}
