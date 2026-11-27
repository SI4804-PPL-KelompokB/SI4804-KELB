<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Penawaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PenawaranApiTest extends TestCase
{
    use RefreshDatabase;

    private Faskes $faskesA;

    private Faskes $faskesB;

    private User $faskesUserA;

    private User $faskesUserB;

    private User $dinkesUser;

    private User $adminUser;

    private User $penyediaUser;

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
    }

    #[Test]
    public function unauthenticated_and_penyedia_users_cannot_access_penawaran_endpoints(): void
    {
        $this->getJson('/api/penawaran')->assertUnauthorized();
        $this->postJson('/api/penawaran', ['catatan' => 'Surplus'])->assertUnauthorized();

        $this->actingAs($this->penyediaUser)->getJson('/api/penawaran')->assertForbidden();
        $this->actingAs($this->penyediaUser)->postJson('/api/penawaran', ['catatan' => 'Surplus'])->assertForbidden();
        $this->actingAs($this->penyediaUser)->get('/penawaran')->assertRedirect();
    }

    #[Test]
    public function faskes_can_crud_own_penawaran_and_dinkes_admin_can_read_only(): void
    {
        // Create
        $createRes = $this->actingAs($this->faskesUserA)
            ->postJson('/api/penawaran', [
                'judul' => 'Surplus Stok Q4',
                'catatan' => 'Simpan suhu ruang, siap diambil',
            ]);

        $createRes->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.faskes_id', $this->faskesA->id)
            ->assertJsonPath('data.status', 'tersedia')
            ->assertJsonPath('data.catatan', 'Simpan suhu ruang, siap diambil');

        $penawaranId = $createRes->json('data.id');

        // Read list & detail
        $this->actingAs($this->faskesUserA)
            ->getJson('/api/penawaran')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($this->faskesUserA)
            ->getJson("/api/penawaran/{$penawaranId}")
            ->assertOk()
            ->assertJsonPath('data.id', $penawaranId);

        $this->actingAs($this->dinkesUser)
            ->getJson('/api/penawaran')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($this->adminUser)
            ->getJson("/api/penawaran/{$penawaranId}")
            ->assertOk();

        // Dinkes & Admin cannot mutate
        $this->actingAs($this->dinkesUser)
            ->putJson("/api/penawaran/{$penawaranId}", ['catatan' => 'Ubah'])
            ->assertForbidden();

        $this->actingAs($this->adminUser)
            ->deleteJson("/api/penawaran/{$penawaranId}")
            ->assertForbidden();

        // Update catatan by owning faskes
        $this->actingAs($this->faskesUserA)
            ->putJson("/api/penawaran/{$penawaranId}", [
                'catatan' => 'Catatan diperbarui untuk Dinkes',
            ])
            ->assertOk()
            ->assertJsonPath('data.catatan', 'Catatan diperbarui untuk Dinkes');

        $this->assertDatabaseHas('penawaran', [
            'id' => $penawaranId,
            'catatan' => 'Catatan diperbarui untuk Dinkes',
        ]);

        // Delete by owning faskes
        $this->actingAs($this->faskesUserA)
            ->deleteJson("/api/penawaran/{$penawaranId}")
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('penawaran', ['id' => $penawaranId]);
    }

    #[Test]
    public function cross_facility_isolation_and_locked_status_are_enforced(): void
    {
        $offer = Penawaran::create([
            'faskes_id' => $this->faskesA->id,
            'user_id' => $this->faskesUserA->id,
            'judul' => 'Surplus A',
            'status' => Penawaran::STATUS_TERSEDIA,
            'catatan' => 'Awal',
        ]);

        // Another faskes gets empty index and 404 on show/update/delete
        $this->actingAs($this->faskesUserB)
            ->getJson('/api/penawaran')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->actingAs($this->faskesUserB)
            ->getJson("/api/penawaran/{$offer->id}")
            ->assertNotFound();

        $this->actingAs($this->faskesUserB)
            ->putJson("/api/penawaran/{$offer->id}", ['catatan' => 'Hack'])
            ->assertNotFound();

        $this->actingAs($this->faskesUserB)
            ->deleteJson("/api/penawaran/{$offer->id}")
            ->assertNotFound();

        // Lock mutations when status is no longer 'tersedia'
        $offer->update(['status' => 'dialokasikan']);

        $this->actingAs($this->faskesUserA)
            ->putJson("/api/penawaran/{$offer->id}", ['catatan' => 'Edit terlambat'])
            ->assertUnprocessable();

        $this->actingAs($this->faskesUserA)
            ->deleteJson("/api/penawaran/{$offer->id}")
            ->assertUnprocessable();
    }

    #[Test]
    public function web_page_renders_for_authorized_roles(): void
    {
        $this->actingAs($this->faskesUserA)
            ->get('/penawaran')
            ->assertOk()
            ->assertSee('Penawaran Surplus');

        $this->actingAs($this->dinkesUser)
            ->get('/penawaran')
            ->assertOk()
            ->assertSee('Mode Lihat Saja');
    }
}
