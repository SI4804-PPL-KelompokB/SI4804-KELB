<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Permintaan;
use App\Models\PermintaanItem;
use App\Models\Urgensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermintaanItemApiTest extends TestCase
{
    use RefreshDatabase;

    private Faskes $faskesA;

    private Faskes $faskesB;

    private User $faskesUserA;

    private User $faskesUserB;

    private User $dinkesUser;

    private User $adminUser;

    private User $penyediaUser;

    private Permintaan $permintaanA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faskesA = Faskes::create([
            'nama_faskes' => 'Puskesmas Cibiru',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Raya Cibiru No. 10',
            'kota' => 'Kota Bandung',
            'kontak' => '022-7801111',
        ]);

        $this->faskesB = Faskes::create([
            'nama_faskes' => 'RSUD Ujungberung',
            'jenis' => 'RSUD',
            'alamat' => 'Jl. Rumah Sakit No. 22',
            'kota' => 'Kota Bandung',
            'kontak' => '022-7802222',
        ]);

        $urgensi = Urgensi::create([
            'nama_level' => 'Kritis',
            'batas_waktu_jam' => 2,
        ]);

        $this->faskesUserA = User::factory()->create([
            'role' => 'faskes',
            'faskes_id' => $this->faskesA->id,
            'is_active' => true,
        ]);

        $this->faskesUserB = User::factory()->create([
            'role' => 'faskes',
            'faskes_id' => $this->faskesB->id,
            'is_active' => true,
        ]);

        $this->dinkesUser = User::factory()->create([
            'role' => 'dinkes',
            'faskes_id' => null,
            'is_active' => true,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'faskes_id' => null,
            'is_active' => true,
        ]);

        $this->penyediaUser = User::factory()->create([
            'role' => 'penyedia',
            'faskes_id' => null,
            'is_active' => true,
        ]);

        $this->permintaanA = Permintaan::create([
            'faskes_id' => $this->faskesA->id,
            'urgensi_id' => $urgensi->id,
            'user_id' => $this->faskesUserA->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
            'catatan' => 'Kebutuhan IGD',
        ]);
    }

    #[Test]
    public function unauthenticated_and_penyedia_users_cannot_access_permintaan_items(): void
    {
        $this->getJson("/api/permintaan/{$this->permintaanA->id}/items")->assertUnauthorized();
        $this->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
            'nama_obat' => 'Paracetamol',
            'qty' => 10,
        ])->assertUnauthorized();

        $this->actingAs($this->penyediaUser)
            ->getJson("/api/permintaan/{$this->permintaanA->id}/items")
            ->assertForbidden();

        $this->actingAs($this->penyediaUser)
            ->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
                'nama_obat' => 'Paracetamol',
                'qty' => 10,
            ])
            ->assertForbidden();
    }

    #[Test]
    public function faskes_owner_can_crud_items_and_dinkes_admin_can_read_only(): void
    {
        // Create item
        $createRes = $this->actingAs($this->faskesUserA)
            ->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
                'nama_obat' => 'Epinephrine 1mg/mL',
                'satuan' => 'Ampul',
                'qty' => 25,
            ]);

        $createRes->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.nama_obat', 'Epinephrine 1mg/mL')
            ->assertJsonPath('data.satuan', 'Ampul')
            ->assertJsonPath('data.qty', 25);

        $itemId = $createRes->json('data.id');

        // Read by faskes owner, dinkes, and admin
        $this->actingAs($this->faskesUserA)
            ->getJson("/api/permintaan/{$this->permintaanA->id}/items")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($this->dinkesUser)
            ->getJson("/api/permintaan/{$this->permintaanA->id}/items")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($this->adminUser)
            ->getJson("/api/permintaan/{$this->permintaanA->id}/items")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        // Dinkes & Admin cannot mutate items
        $this->actingAs($this->dinkesUser)
            ->putJson("/api/permintaan/items/{$itemId}", ['qty' => 50])
            ->assertForbidden();

        $this->actingAs($this->adminUser)
            ->deleteJson("/api/permintaan/items/{$itemId}")
            ->assertForbidden();

        // Update qty by faskes owner
        $this->actingAs($this->faskesUserA)
            ->putJson("/api/permintaan/items/{$itemId}", ['qty' => 40])
            ->assertOk()
            ->assertJsonPath('data.qty', 40);

        $this->assertDatabaseHas('permintaan_items', [
            'id' => $itemId,
            'qty' => 40,
        ]);

        // Delete item by faskes owner
        $this->actingAs($this->faskesUserA)
            ->deleteJson("/api/permintaan/items/{$itemId}")
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('permintaan_items', ['id' => $itemId]);
    }

    #[Test]
    public function other_faskes_cannot_view_or_mutate_items_and_non_diajukan_is_locked(): void
    {
        $item = PermintaanItem::create([
            'permintaan_id' => $this->permintaanA->id,
            'nama_obat' => 'Amoxicillin 500mg',
            'satuan' => 'Box',
            'qty' => 15,
        ]);

        // Cross-facility isolation returns 404
        $this->actingAs($this->faskesUserB)
            ->getJson("/api/permintaan/{$this->permintaanA->id}/items")
            ->assertNotFound();

        $this->actingAs($this->faskesUserB)
            ->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
                'nama_obat' => 'Ibuprofen',
                'qty' => 5,
            ])
            ->assertNotFound();

        $this->actingAs($this->faskesUserB)
            ->putJson("/api/permintaan/items/{$item->id}", ['qty' => 99])
            ->assertNotFound();

        $this->actingAs($this->faskesUserB)
            ->deleteJson("/api/permintaan/items/{$item->id}")
            ->assertNotFound();

        // Validation failure check
        $this->actingAs($this->faskesUserA)
            ->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
                'nama_obat' => '',
                'qty' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_obat', 'qty']);

        // Lock mutations when parent permintaan status is no longer 'diajukan'
        $this->permintaanA->update(['status' => 'disetujui']);

        $this->actingAs($this->faskesUserA)
            ->postJson("/api/permintaan/{$this->permintaanA->id}/items", [
                'nama_obat' => 'Ceftriaxone',
                'qty' => 10,
            ])
            ->assertUnprocessable();

        $this->actingAs($this->faskesUserA)
            ->putJson("/api/permintaan/items/{$item->id}", ['qty' => 20])
            ->assertUnprocessable();

        $this->actingAs($this->faskesUserA)
            ->deleteJson("/api/permintaan/items/{$item->id}")
            ->assertUnprocessable();
    }
}
