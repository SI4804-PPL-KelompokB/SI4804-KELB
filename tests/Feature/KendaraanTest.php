<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KendaraanTest extends TestCase
{
    use RefreshDatabase;

    protected User $penyediaUser;

    protected User $penyediaLain;

    protected User $dinkesUser;

    protected Faskes $faskesPenyedia;

    protected Faskes $faskesLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faskesPenyedia = Faskes::create([
            'nama_faskes' => 'Gudang Farmasi Utama',
            'jenis' => 'Gudang Farmasi',
            'alamat' => 'Jl. Farmasi No. 10',
            'kota' => 'Kota Bandung',
        ]);

        $this->faskesLain = Faskes::create([
            'nama_faskes' => 'Gudang Farmasi Cabang',
            'jenis' => 'Gudang Farmasi',
            'alamat' => 'Jl. Cabang No. 20',
            'kota' => 'Kota Bandung',
        ]);

        $this->penyediaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'penyedia@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'faskes_id' => $this->faskesPenyedia->id,
            'is_active' => true,
        ]);

        $this->penyediaLain = User::create([
            'name' => 'Santoso Lain',
            'email' => 'penyedia2@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'faskes_id' => $this->faskesLain->id,
            'is_active' => true,
        ]);

        $this->dinkesUser = User::create([
            'name' => 'Dr. Hendra',
            'email' => 'dinkes@bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'dinkes',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_kendaraan_index(): void
    {
        $response = $this->get(route('penyedia.kendaraan.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_penyedia_role_cannot_access_kendaraan_index(): void
    {
        $response = $this->actingAs($this->dinkesUser)->get(route('penyedia.kendaraan.index'));
        $response->assertRedirect(route('dinkes.dashboard'));
    }

    public function test_penyedia_can_view_kendaraan_list(): void
    {
        Kendaraan::create([
            'faskes_id' => $this->faskesPenyedia->id,
            'nama_kurir' => 'Ahmad Suryadi',
            'jenis_kendaraan' => 'Ambulans',
            'plat_nomor' => 'D 1234 MED',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->get(route('penyedia.kendaraan.index'));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Suryadi');
        $response->assertSee('D 1234 MED');
    }

    public function test_penyedia_can_view_create_page(): void
    {
        $response = $this->actingAs($this->penyediaUser)->get(route('penyedia.kendaraan.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Kendaraan / Kurir Baru');
    }

    public function test_penyedia_can_store_kendaraan(): void
    {
        $data = [
            'nama_kurir' => 'Rizki Firmansyah',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 5678 OBT',
            'nomor_telepon' => '081234567890',
            'status' => 'aktif',
            'keterangan' => 'Kurir motor cepat.',
        ];

        $response = $this->actingAs($this->penyediaUser)->post(route('penyedia.kendaraan.store'), $data);
        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kendaraan', [
            'faskes_id' => $this->faskesPenyedia->id,
            'plat_nomor' => 'D 5678 OBT',
            'nama_kurir' => 'Rizki Firmansyah',
        ]);
    }

    public function test_store_fails_with_duplicate_plat_nomor(): void
    {
        Kendaraan::create([
            'faskes_id' => $this->faskesPenyedia->id,
            'nama_kurir' => 'Ahmad',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 1111 DUP',
            'status' => 'aktif',
        ]);

        $data = [
            'nama_kurir' => 'Budi Baru',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 1111 DUP',
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->penyediaUser)->post(route('penyedia.kendaraan.store'), $data);
        $response->assertSessionHasErrors('plat_nomor');
    }

    public function test_penyedia_can_view_edit_page_of_own_kendaraan(): void
    {
        $kendaraan = Kendaraan::create([
            'faskes_id' => $this->faskesPenyedia->id,
            'nama_kurir' => 'Ahmad',
            'jenis_kendaraan' => 'Mobil Box',
            'plat_nomor' => 'D 9999 BOX',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->get(route('penyedia.kendaraan.edit', $kendaraan));
        $response->assertStatus(200);
        $response->assertSee('D 9999 BOX');
    }

    public function test_penyedia_cannot_view_edit_page_of_other_faskes_kendaraan(): void
    {
        $kendaraanLain = Kendaraan::create([
            'faskes_id' => $this->faskesLain->id,
            'nama_kurir' => 'Kurir Asing',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 8888 ASG',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->get(route('penyedia.kendaraan.edit', $kendaraanLain));
        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('error');
    }

    public function test_penyedia_can_update_own_kendaraan(): void
    {
        $kendaraan = Kendaraan::create([
            'faskes_id' => $this->faskesPenyedia->id,
            'nama_kurir' => 'Kurir Lama',
            'jenis_kendaraan' => 'Pick Up',
            'plat_nomor' => 'D 7777 PCK',
            'status' => 'aktif',
        ]);

        $updateData = [
            'nama_kurir' => 'Kurir Baru Update',
            'jenis_kendaraan' => 'Pick Up',
            'plat_nomor' => 'D 7777 PCK',
            'status' => 'maintenance',
            'keterangan' => 'Sedang perbaikan',
        ];

        $response = $this->actingAs($this->penyediaUser)->put(route('penyedia.kendaraan.update', $kendaraan), $updateData);
        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kendaraan', [
            'id' => $kendaraan->id,
            'nama_kurir' => 'Kurir Baru Update',
            'status' => 'maintenance',
        ]);
    }

    public function test_penyedia_cannot_update_other_faskes_kendaraan(): void
    {
        $kendaraanLain = Kendaraan::create([
            'faskes_id' => $this->faskesLain->id,
            'nama_kurir' => 'Kurir Asing',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 6666 ASG',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->put(route('penyedia.kendaraan.update', $kendaraanLain), [
            'nama_kurir' => 'Hacker Kurir',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 6666 ASG',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('error');
    }

    public function test_penyedia_can_delete_own_kendaraan(): void
    {
        $kendaraan = Kendaraan::create([
            'faskes_id' => $this->faskesPenyedia->id,
            'nama_kurir' => 'Kurir Hapus',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 4444 DEL',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->delete(route('penyedia.kendaraan.destroy', $kendaraan));
        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kendaraan', [
            'id' => $kendaraan->id,
        ]);
    }

    public function test_penyedia_cannot_delete_other_faskes_kendaraan(): void
    {
        $kendaraanLain = Kendaraan::create([
            'faskes_id' => $this->faskesLain->id,
            'nama_kurir' => 'Kurir Asing',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'D 3333 ASG',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->penyediaUser)->delete(route('penyedia.kendaraan.destroy', $kendaraanLain));
        $response->assertRedirect(route('penyedia.kendaraan.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('kendaraan', [
            'id' => $kendaraanLain->id,
        ]);
    }
}
