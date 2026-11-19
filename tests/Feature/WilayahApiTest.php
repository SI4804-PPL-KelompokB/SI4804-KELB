<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PBI-01: Feature tests untuk API Manajemen Wilayah.
 * Menguji seluruh endpoint CRUD /api/wilayah sesuai spesifikasi.
 */
class WilayahApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Test Users ───────────────────────────────────────────────
    private User $admin;
    private User $dinkes;
    private User $faskes;
    private User $penyedia;
    private Faskes $faksesEntity;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat faskes entity untuk user faskes & penyedia
        $this->faksesEntity = Faskes::create([
            'nama_faskes' => 'Puskesmas Sukamaju',
            'jenis'       => 'Puskesmas',
            'alamat'      => 'Jl. Sukamaju No. 18',
            'kota'        => 'Kota Bandung',
        ]);

        $this->admin = User::create([
            'name'      => 'Admin MedStock',
            'email'     => 'admin@medstock.id',
            'password'  => Hash::make('password123'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->dinkes = User::create([
            'name'      => 'Dr. Hendra Gunawan',
            'email'     => 'dinkes@bandung.go.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dinkes',
            'is_active' => true,
        ]);

        $this->faskes = User::create([
            'name'      => 'Ayu Nuraini',
            'email'     => 'ayu.nuraini@puskesmas.go.id',
            'password'  => Hash::make('password123'),
            'role'      => 'faskes',
            'faskes_id' => $this->faksesEntity->id,
            'is_active' => true,
        ]);

        $this->penyedia = User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'penyedia@farmasikota.id',
            'password'  => Hash::make('password123'),
            'role'      => 'penyedia',
            'faskes_id' => $this->faksesEntity->id,
            'is_active' => true,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // GET /api/wilayah — INDEX
    // ══════════════════════════════════════════════════════════════

    #[Test]
    public function unauthenticated_user_cannot_access_wilayah_list(): void
    {
        $response = $this->getJson('/api/wilayah');

        $response->assertStatus(401);
    }
    #[Test]
public function web_login_session_can_authenticate_api_requests(): void
{
    // Login melalui endpoint web, bukan actingAs().
    $loginResponse = $this->post('/login', [
        'email' => 'admin@medstock.id',
        'password' => 'password123',
        'role' => 'admin',
    ]);

    $loginResponse->assertRedirect();

    // Gunakan session hasil login untuk mengakses API.
    $apiResponse = $this->getJson('/api/wilayah');

    $apiResponse->assertStatus(200)
        ->assertJsonPath('status', 'success');
}

    #[Test]
    public function authenticated_admin_can_get_wilayah_list(): void
    {
        Wilayah::create(['nama_wilayah' => 'Wilayah Utara', 'deskripsi' => 'Bagian utara']);
        Wilayah::create(['nama_wilayah' => 'Wilayah Selatan']);

        $response = $this->actingAs($this->admin)->getJson('/api/wilayah');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [['id', 'nama_wilayah', 'deskripsi', 'created_at', 'updated_at']],
            ])
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data');
    }

    #[Test]
    public function authenticated_dinkes_can_get_wilayah_list(): void
    {
        $response = $this->actingAs($this->dinkes)->getJson('/api/wilayah');
        $response->assertStatus(200)->assertJsonPath('status', 'success');
    }

    #[Test]
    public function authenticated_faskes_can_get_wilayah_list(): void
    {
        $response = $this->actingAs($this->faskes)->getJson('/api/wilayah');
        $response->assertStatus(200)->assertJsonPath('status', 'success');
    }

    #[Test]
    public function authenticated_penyedia_can_get_wilayah_list(): void
    {
        $response = $this->actingAs($this->penyedia)->getJson('/api/wilayah');
        $response->assertStatus(200)->assertJsonPath('status', 'success');
    }

    #[Test]
    public function wilayah_list_is_empty_when_no_records_exist(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/wilayah');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(0, 'data');
    }

    // ══════════════════════════════════════════════════════════════
    // POST /api/wilayah — STORE
    // ══════════════════════════════════════════════════════════════

    #[Test]
    public function unauthenticated_user_cannot_create_wilayah(): void
    {
        $response = $this->postJson('/api/wilayah', ['nama_wilayah' => 'Wilayah Baru']);
        $response->assertStatus(401);
    }

    #[Test]
    public function admin_can_create_wilayah(): void
    {
        $payload = [
            'nama_wilayah' => 'Wilayah Bandung Utara',
            'deskripsi'    => 'Zona faskes bagian utara kota.',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/wilayah', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.nama_wilayah', 'Wilayah Bandung Utara')
            ->assertJsonPath('data.deskripsi', 'Zona faskes bagian utara kota.');

        $this->assertDatabaseHas('wilayah', ['nama_wilayah' => 'Wilayah Bandung Utara']);
    }

    #[Test]
    public function dinkes_can_create_wilayah(): void
    {
        $response = $this->actingAs($this->dinkes)->postJson('/api/wilayah', [
            'nama_wilayah' => 'Wilayah Bandung Selatan',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('wilayah', ['nama_wilayah' => 'Wilayah Bandung Selatan']);
    }

    #[Test]
    public function wilayah_can_be_created_without_deskripsi(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/wilayah', [
            'nama_wilayah' => 'Wilayah Tanpa Deskripsi',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('wilayah', [
            'nama_wilayah' => 'Wilayah Tanpa Deskripsi',
            'deskripsi'    => null,
        ]);
    }

    #[Test]
    public function faskes_role_cannot_create_wilayah(): void
    {
        $response = $this->actingAs($this->faskes)->postJson('/api/wilayah', [
            'nama_wilayah' => 'Wilayah Tidak Sah',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function penyedia_role_cannot_create_wilayah(): void
    {
        $response = $this->actingAs($this->penyedia)->postJson('/api/wilayah', [
            'nama_wilayah' => 'Wilayah Tidak Sah',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function create_wilayah_fails_with_missing_nama_wilayah(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/wilayah', [
            'deskripsi' => 'Deskripsi saja tanpa nama.',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_wilayah']);
    }

    #[Test]
    public function create_wilayah_fails_with_duplicate_nama_wilayah(): void
    {
        Wilayah::create(['nama_wilayah' => 'Wilayah Duplikat']);

        $response = $this->actingAs($this->admin)->postJson('/api/wilayah', [
            'nama_wilayah' => 'Wilayah Duplikat',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_wilayah']);
    }

    #[Test]
    public function create_wilayah_fails_when_nama_wilayah_exceeds_max_length(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/wilayah', [
            'nama_wilayah' => str_repeat('A', 256),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_wilayah']);
    }

    // ══════════════════════════════════════════════════════════════
    // GET /api/wilayah/{id} — SHOW
    // ══════════════════════════════════════════════════════════════

    #[Test]
    public function authenticated_user_can_view_single_wilayah(): void
    {
        $wilayah = Wilayah::create([
            'nama_wilayah' => 'Wilayah Bandung Timur',
            'deskripsi'    => 'Zona timur kota.',
        ]);

        $response = $this->actingAs($this->faskes)->getJson("/api/wilayah/{$wilayah->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $wilayah->id)
            ->assertJsonPath('data.nama_wilayah', 'Wilayah Bandung Timur');
    }

    #[Test]
    public function show_wilayah_returns_404_for_nonexistent_id(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/wilayah/99999');

        $response->assertStatus(404)
            ->assertJsonPath('status', 'error');
    }

    #[Test]
    public function unauthenticated_user_cannot_view_single_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Test']);

        $response = $this->getJson("/api/wilayah/{$wilayah->id}");
        $response->assertStatus(401);
    }

    // ══════════════════════════════════════════════════════════════
    // PUT /api/wilayah/{id} — UPDATE
    // ══════════════════════════════════════════════════════════════

    #[Test]
    public function admin_can_update_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Lama', 'deskripsi' => 'Deskripsi lama.']);

        $response = $this->actingAs($this->admin)->putJson("/api/wilayah/{$wilayah->id}", [
            'nama_wilayah' => 'Wilayah Diperbarui',
            'deskripsi'    => 'Deskripsi baru.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.nama_wilayah', 'Wilayah Diperbarui')
            ->assertJsonPath('data.deskripsi', 'Deskripsi baru.');

        $this->assertDatabaseHas('wilayah', ['id' => $wilayah->id, 'nama_wilayah' => 'Wilayah Diperbarui']);
        $this->assertDatabaseMissing('wilayah', ['nama_wilayah' => 'Wilayah Lama']);
    }

    #[Test]
    public function dinkes_can_update_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Dinkes Edit']);

        $response = $this->actingAs($this->dinkes)->putJson("/api/wilayah/{$wilayah->id}", [
            'nama_wilayah' => 'Wilayah Dinkes Sudah Diedit',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', 'success');
    }

    #[Test]
    public function update_wilayah_allows_same_name_for_same_record(): void
    {
        // Update dengan nama yang sama (tidak boleh trigger unique error)
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Sama']);

        $response = $this->actingAs($this->admin)->putJson("/api/wilayah/{$wilayah->id}", [
            'nama_wilayah' => 'Wilayah Sama',
            'deskripsi'    => 'Deskripsi baru untuk nama yang sama.',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', 'success');
    }

    #[Test]
    public function faskes_role_cannot_update_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Protected']);

        $response = $this->actingAs($this->faskes)->putJson("/api/wilayah/{$wilayah->id}", [
            'nama_wilayah' => 'Wilayah Diubah Faskes',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function update_wilayah_returns_404_for_nonexistent_id(): void
    {
        $response = $this->actingAs($this->admin)->putJson('/api/wilayah/99999', [
            'nama_wilayah' => 'Tidak Ada',
        ]);

        $response->assertStatus(404)->assertJsonPath('status', 'error');
    }

    #[Test]
    public function update_wilayah_fails_with_duplicate_nama_wilayah(): void
    {
        Wilayah::create(['nama_wilayah' => 'Wilayah Sudah Ada']);
        $target = Wilayah::create(['nama_wilayah' => 'Wilayah Target']);

        $response = $this->actingAs($this->admin)->putJson("/api/wilayah/{$target->id}", [
            'nama_wilayah' => 'Wilayah Sudah Ada',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['nama_wilayah']);
    }

    // ══════════════════════════════════════════════════════════════
    // DELETE /api/wilayah/{id} — DESTROY
    // ══════════════════════════════════════════════════════════════

    #[Test]
    public function admin_can_delete_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Dihapus']);

        $response = $this->actingAs($this->admin)->deleteJson("/api/wilayah/{$wilayah->id}");

        $response->assertStatus(200)->assertJsonPath('status', 'success');
        $this->assertDatabaseMissing('wilayah', ['id' => $wilayah->id]);
    }

    #[Test]
    public function dinkes_can_delete_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Dihapus Dinkes']);

        $response = $this->actingAs($this->dinkes)->deleteJson("/api/wilayah/{$wilayah->id}");

        $response->assertStatus(200)->assertJsonPath('status', 'success');
        $this->assertDatabaseMissing('wilayah', ['id' => $wilayah->id]);
    }

    #[Test]
    public function faskes_role_cannot_delete_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Protected Delete']);

        $response = $this->actingAs($this->faskes)->deleteJson("/api/wilayah/{$wilayah->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('wilayah', ['id' => $wilayah->id]);
    }

    #[Test]
    public function penyedia_role_cannot_delete_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Protected Penyedia']);

        $response = $this->actingAs($this->penyedia)->deleteJson("/api/wilayah/{$wilayah->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function delete_wilayah_returns_404_for_nonexistent_id(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson('/api/wilayah/99999');
        $response->assertStatus(404)->assertJsonPath('status', 'error');
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_wilayah(): void
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Unauthenticated']);

        $response = $this->deleteJson("/api/wilayah/{$wilayah->id}");
        $response->assertStatus(401);
    }
}
