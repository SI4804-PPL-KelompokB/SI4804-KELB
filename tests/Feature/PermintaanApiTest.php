<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Permintaan;
use App\Models\Urgensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermintaanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $dinkes;

    private User $faskesAUser;

    private User $faskesBUser;

    private User $penyedia;

    private Faskes $faskesA;

    private Faskes $faskesB;

    private Urgensi $urgensiTinggi;

    private Urgensi $urgensiNormal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faskesA = Faskes::create([
            'nama_faskes' => 'Puskesmas Sukamaju',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Sukamaju No. 18',
            'kota' => 'Kota Bandung',
        ]);

        $this->faskesB = Faskes::create([
            'nama_faskes' => 'RSUD Kota Bandung',
            'jenis' => 'Rumah Sakit',
            'alamat' => 'Jl. Rumah Sakit No. 22',
            'kota' => 'Kota Bandung',
        ]);

        $this->urgensiTinggi = Urgensi::create([
            'nama_level' => 'Level Prioritas 1',
            'batas_waktu_jam' => 2,
        ]);

        $this->urgensiNormal = Urgensi::create([
            'nama_level' => 'Level Prioritas 2',
            'batas_waktu_jam' => 12,
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

        $this->faskesAUser = User::create([
            'name' => 'Ayu Nuraini',
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $this->faskesA->id,
            'is_active' => true,
        ]);

        $this->faskesBUser = User::create([
            'name' => 'Rina Marlina',
            'email' => 'rina.marlina@rsudbandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $this->faskesB->id,
            'is_active' => true,
        ]);

        $this->penyedia = User::create([
            'name' => 'Budi Santoso',
            'email' => 'penyedia@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'faskes_id' => $this->faskesA->id,
            'is_active' => true,
        ]);
    }

    // =========================================================================
    // Authentication & Stateful Session Tests
    // =========================================================================

    #[Test]
    public function unauthenticated_user_cannot_access_any_permintaan_endpoint(): void
    {
        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->getJson('/api/permintaan')->assertStatus(401);
        $this->postJson('/api/permintaan', ['urgensi_id' => $this->urgensiTinggi->id])->assertStatus(401);
        $this->getJson("/api/permintaan/{$permintaan->id}")->assertStatus(401);
        $this->putJson("/api/permintaan/{$permintaan->id}", ['urgensi_id' => $this->urgensiNormal->id])->assertStatus(401);
        $this->deleteJson("/api/permintaan/{$permintaan->id}")->assertStatus(401);
    }

    #[Test]
    public function web_login_session_can_authenticate_permintaan_api_requests(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => 'password123',
            'role' => 'faskes',
        ]);

        $loginResponse->assertRedirect();

        $apiResponse = $this->postJson('/api/permintaan', [
            'urgensi_id' => $this->urgensiTinggi->id,
            'catatan' => 'Permintaan via sesi login web.',
        ]);

        $apiResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.faskes_id', $this->faskesA->id);
    }

    // =========================================================================
    // Role-Based Authorization & Account Guard Tests
    // =========================================================================

    #[Test]
    public function penyedia_role_is_forbidden_from_all_permintaan_endpoints(): void
    {
        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($this->penyedia)->getJson('/api/permintaan')->assertStatus(403);
        $this->actingAs($this->penyedia)->postJson('/api/permintaan', ['urgensi_id' => $this->urgensiTinggi->id])->assertStatus(403);
        $this->actingAs($this->penyedia)->getJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
        $this->actingAs($this->penyedia)->putJson("/api/permintaan/{$permintaan->id}", ['urgensi_id' => $this->urgensiNormal->id])->assertStatus(403);
        $this->actingAs($this->penyedia)->deleteJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
    }

    #[Test]
    public function dinkes_and_admin_can_read_all_facilities_but_cannot_mutate_permintaan(): void
    {
        $reqA = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $reqB = Permintaan::create([
            'faskes_id' => $this->faskesB->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesBUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        foreach ([$this->dinkes, $this->admin] as $reviewer) {
            $this->actingAs($reviewer)->getJson('/api/permintaan')
                ->assertStatus(200)
                ->assertJsonPath('status', 'success')
                ->assertJsonCount(2, 'data');

            $this->actingAs($reviewer)->getJson("/api/permintaan/{$reqA->id}")
                ->assertStatus(200)
                ->assertJsonPath('data.faskes.nama_faskes', 'Puskesmas Sukamaju');

            $this->actingAs($reviewer)->getJson("/api/permintaan/{$reqB->id}")
                ->assertStatus(200)
                ->assertJsonPath('data.faskes.nama_faskes', 'RSUD Kota Bandung');

            $this->actingAs($reviewer)->postJson('/api/permintaan', ['urgensi_id' => $this->urgensiTinggi->id])->assertStatus(403);
            $this->actingAs($reviewer)->putJson("/api/permintaan/{$reqA->id}", ['urgensi_id' => $this->urgensiNormal->id])->assertStatus(403);
            $this->actingAs($reviewer)->deleteJson("/api/permintaan/{$reqA->id}")->assertStatus(403);
        }
    }

    #[Test]
    public function inactive_faskes_user_is_forbidden_from_all_permintaan_endpoints(): void
    {
        $inactiveFaskes = User::create([
            'name' => 'Petugas Nonaktif',
            'email' => 'nonaktif@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $this->faskesA->id,
            'is_active' => false,
        ]);

        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($inactiveFaskes)->getJson('/api/permintaan')->assertStatus(403);
        $this->actingAs($inactiveFaskes)->postJson('/api/permintaan', ['urgensi_id' => $this->urgensiTinggi->id])->assertStatus(403);
        $this->actingAs($inactiveFaskes)->getJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
        $this->actingAs($inactiveFaskes)->putJson("/api/permintaan/{$permintaan->id}", ['urgensi_id' => $this->urgensiNormal->id])->assertStatus(403);
        $this->actingAs($inactiveFaskes)->deleteJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
    }

    #[Test]
    public function faskes_user_with_null_faskes_id_is_forbidden_gracefully(): void
    {
        $unassignedFaskes = User::create([
            'name' => 'Petugas Tanpa Faskes',
            'email' => 'unassigned@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => null,
            'is_active' => true,
        ]);

        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($unassignedFaskes)->getJson('/api/permintaan')->assertStatus(403);
        $this->actingAs($unassignedFaskes)->postJson('/api/permintaan', ['urgensi_id' => $this->urgensiTinggi->id])->assertStatus(403);
        $this->actingAs($unassignedFaskes)->getJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
        $this->actingAs($unassignedFaskes)->putJson("/api/permintaan/{$permintaan->id}", ['urgensi_id' => $this->urgensiNormal->id])->assertStatus(403);
        $this->actingAs($unassignedFaskes)->deleteJson("/api/permintaan/{$permintaan->id}")->assertStatus(403);
    }

    // =========================================================================
    // Create (POST /api/permintaan) Tests
    // =========================================================================

    #[Test]
    public function faskes_user_can_create_emergency_request_and_relations_are_scoped(): void
    {
        $response = $this->actingAs($this->faskesAUser)->postJson('/api/permintaan', [
            'urgensi_id' => $this->urgensiTinggi->id,
            'catatan' => 'Stok epinefrin dan cairan infus menipis.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.faskes_id', $this->faskesA->id)
            ->assertJsonPath('data.user_id', $this->faskesAUser->id)
            ->assertJsonPath('data.urgensi_id', $this->urgensiTinggi->id)
            ->assertJsonPath('data.status', 'diajukan')
            ->assertJsonPath('data.catatan', 'Stok epinefrin dan cairan infus menipis.')
            ->assertJsonPath('data.urgensi.nama_level', 'Level Prioritas 1')
            ->assertJsonPath('data.faskes.nama_faskes', 'Puskesmas Sukamaju')
            ->assertJsonMissingPath('data.user')
            ->assertJsonMissingPath('data.faskes.telepon')
            ->assertJsonMissingPath('data.faskes.alamat');

        $this->assertDatabaseHas('permintaan', [
            'faskes_id' => $this->faskesA->id,
            'user_id' => $this->faskesAUser->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'status' => 'diajukan',
        ]);
    }

    #[Test]
    public function faskes_user_can_create_emergency_request_without_catatan(): void
    {
        $response = $this->actingAs($this->faskesAUser)->postJson('/api/permintaan', [
            'urgensi_id' => $this->urgensiNormal->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.catatan', null);
    }

    #[Test]
    public function create_permintaan_ignores_client_spoofed_faskes_id_user_id_and_status(): void
    {
        $response = $this->actingAs($this->faskesAUser)->postJson('/api/permintaan', [
            'urgensi_id' => $this->urgensiTinggi->id,
            'faskes_id' => $this->faskesB->id,
            'user_id' => $this->faskesBUser->id,
            'status' => 'disetujui',
            'catatan' => 'Uji keamanan spoofing.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.faskes_id', $this->faskesA->id)
            ->assertJsonPath('data.user_id', $this->faskesAUser->id)
            ->assertJsonPath('data.status', 'diajukan');

        $this->assertDatabaseHas('permintaan', [
            'faskes_id' => $this->faskesA->id,
            'user_id' => $this->faskesAUser->id,
            'status' => 'diajukan',
            'catatan' => 'Uji keamanan spoofing.',
        ]);
    }

    #[Test]
    public function create_permintaan_fails_validation_with_missing_or_invalid_urgensi_id(): void
    {
        $this->actingAs($this->faskesAUser)->postJson('/api/permintaan', [
            'catatan' => 'Tanpa urgensi.',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['urgensi_id']);

        $this->actingAs($this->faskesAUser)->postJson('/api/permintaan', [
            'urgensi_id' => 99999,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['urgensi_id']);
    }

    // =========================================================================
    // Read & Cross-Facility Isolation (GET /api/permintaan & /{id}) Tests
    // =========================================================================

    #[Test]
    public function faskes_index_returns_only_own_facility_requests(): void
    {
        Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
            'catatan' => 'Permintaan Faskes A',
        ]);

        Permintaan::create([
            'faskes_id' => $this->faskesB->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesBUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
            'catatan' => 'Permintaan Faskes B',
        ]);

        $response = $this->actingAs($this->faskesAUser)->getJson('/api/permintaan');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.faskes_id', $this->faskesA->id)
            ->assertJsonPath('data.0.catatan', 'Permintaan Faskes A');
    }

    #[Test]
    public function faskes_can_view_own_request_but_receives_404_for_other_facility_request(): void
    {
        $ownRequest = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $otherFacilityRequest = Permintaan::create([
            'faskes_id' => $this->faskesB->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesBUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($this->faskesAUser)->getJson("/api/permintaan/{$ownRequest->id}")
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $ownRequest->id)
            ->assertJsonMissingPath('data.user');

        // Cross-facility non-disclosure: must return 404, not 403
        $this->actingAs($this->faskesAUser)->getJson("/api/permintaan/{$otherFacilityRequest->id}")
            ->assertStatus(404)
            ->assertJsonPath('status', 'error');

        $this->actingAs($this->faskesAUser)->getJson('/api/permintaan/99999')
            ->assertStatus(404)
            ->assertJsonPath('status', 'error');
    }

    // =========================================================================
    // Update Urgency (PUT /api/permintaan/{id}) Tests
    // =========================================================================

    #[Test]
    public function owning_faskes_can_update_urgensi_only_when_status_is_diajukan(): void
    {
        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
            'catatan' => 'Catatan awal tidak boleh berubah.',
        ]);

        $response = $this->actingAs($this->faskesAUser)->putJson("/api/permintaan/{$permintaan->id}", [
            'urgensi_id' => $this->urgensiTinggi->id,
            'catatan' => 'Mencoba mengubah catatan.',
            'faskes_id' => $this->faskesB->id,
            'user_id' => $this->faskesBUser->id,
            'status' => 'disetujui',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.urgensi_id', $this->urgensiTinggi->id)
            ->assertJsonPath('data.urgensi.nama_level', 'Level Prioritas 1')
            ->assertJsonPath('data.catatan', 'Catatan awal tidak boleh berubah.')
            ->assertJsonPath('data.faskes_id', $this->faskesA->id)
            ->assertJsonPath('data.user_id', $this->faskesAUser->id)
            ->assertJsonPath('data.status', 'diajukan');

        $this->assertDatabaseHas('permintaan', [
            'id' => $permintaan->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'catatan' => 'Catatan awal tidak boleh berubah.',
            'faskes_id' => $this->faskesA->id,
            'user_id' => $this->faskesAUser->id,
            'status' => 'diajukan',
        ]);
    }

    #[Test]
    public function update_permintaan_returns_404_for_other_facility_and_422_when_not_diajukan(): void
    {
        $otherFacilityRequest = Permintaan::create([
            'faskes_id' => $this->faskesB->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesBUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($this->faskesAUser)->putJson("/api/permintaan/{$otherFacilityRequest->id}", [
            'urgensi_id' => $this->urgensiTinggi->id,
        ])->assertStatus(404)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseHas('permintaan', [
            'id' => $otherFacilityRequest->id,
            'urgensi_id' => $this->urgensiNormal->id,
        ]);

        $processedRequest = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesAUser->id,
            'status' => 'disetujui',
        ]);

        $this->actingAs($this->faskesAUser)->putJson("/api/permintaan/{$processedRequest->id}", [
            'urgensi_id' => $this->urgensiTinggi->id,
        ])->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseHas('permintaan', [
            'id' => $processedRequest->id,
            'urgensi_id' => $this->urgensiNormal->id,
        ]);
    }

    // =========================================================================
    // Delete (DELETE /api/permintaan/{id}) Tests
    // =========================================================================

    #[Test]
    public function owning_faskes_can_delete_request_when_status_is_diajukan(): void
    {
        $permintaan = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiTinggi->id,
            'user_id' => $this->faskesAUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $response = $this->actingAs($this->faskesAUser)->deleteJson("/api/permintaan/{$permintaan->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('permintaan', ['id' => $permintaan->id]);
    }

    #[Test]
    public function delete_permintaan_returns_404_for_other_facility_and_422_when_not_diajukan(): void
    {
        $otherFacilityRequest = Permintaan::create([
            'faskes_id' => $this->faskesB->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesBUser->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($this->faskesAUser)->deleteJson("/api/permintaan/{$otherFacilityRequest->id}")
            ->assertStatus(404)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseHas('permintaan', ['id' => $otherFacilityRequest->id]);

        $processedRequest = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $this->urgensiNormal->id,
            'user_id' => $this->faskesAUser->id,
            'status' => 'disetujui',
        ]);

        $this->actingAs($this->faskesAUser)->deleteJson("/api/permintaan/{$processedRequest->id}")
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseHas('permintaan', ['id' => $processedRequest->id]);
    }
}
