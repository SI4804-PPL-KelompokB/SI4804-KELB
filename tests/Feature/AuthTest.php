<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $faskes = Faskes::create([
            'nama_faskes' => 'Puskesmas Sukamaju',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Sukamaju No. 18',
            'kota' => 'Kota Bandung',
        ]);

        User::create([
            'name' => 'Ayu Nuraini',
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $faskes->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dr. Hendra Gunawan',
            'email' => 'dinkes@bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'dinkes',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'penyedia@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Admin MedStock',
            'email' => 'admin@medstock.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    /**
     * Halaman login dapat dimuat dengan baik.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat datang kembali');
        $response->assertSee('MedStock');
        $response->assertSee('Peran pengguna');
    }

    /**
     * Login berhasil untuk peran Faskes dan diarahkan ke /faskes/dashboard.
     */
    public function test_faskes_user_can_authenticate_and_redirects_to_faskes_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => 'password123',
            'role' => 'faskes',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/faskes/dashboard');
    }

    /**
     * Login berhasil untuk peran Dinkes dan diarahkan ke /dinkes/dashboard.
     */
    public function test_dinkes_user_can_authenticate_and_redirects_to_dinkes_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'dinkes@bandung.go.id',
            'password' => 'password123',
            'role' => 'dinkes',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dinkes/dashboard');
    }

    /**
     * Login berhasil untuk peran Penyedia dan diarahkan ke /penyedia/dashboard.
     */
    public function test_penyedia_user_can_authenticate_and_redirects_to_penyedia_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'penyedia@farmasikota.id',
            'password' => 'password123',
            'role' => 'penyedia',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/penyedia/dashboard');
    }

    /**
     * Login berhasil untuk peran Admin dan diarahkan ke /admin/dashboard.
     */
    public function test_admin_user_can_authenticate_and_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@medstock.id',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Login gagal jika kata sandi salah.
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => 'wrong-password',
            'role' => 'faskes',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Login gagal jika peran yang dipilih tidak sesuai hak akses akun.
     */
    public function test_users_cannot_authenticate_with_mismatched_role(): void
    {
        // Pengguna faskes mencoba login dengan memilih peran admin
        $response = $this->post('/login', [
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('role');
    }

    /**
     * Logout berhasil mengeluarkan pengguna dari sesi dan diarahkan kembali ke login.
     */
    public function test_user_can_logout_successfully(): void
    {
        $user = User::where('email', 'ayu.nuraini@puskesmas.go.id')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Anda telah berhasil keluar dari sistem MedStock.id.');
    }

    /**
     * Tamu (Guest) tidak dapat membuka dashboard tanpa login terlebih dahulu.
     */
    public function test_guest_cannot_access_protected_dashboards(): void
    {
        $response = $this->get('/faskes/dashboard');
        $response->assertRedirect('/login');

        $response2 = $this->get('/admin/dashboard');
        $response2->assertRedirect('/login');
    }

    /**
     * Pengguna dengan peran Faskes tidak boleh mengakses dashboard Admin (Otorisasi NF1).
     */
    public function test_faskes_user_cannot_access_admin_dashboard(): void
    {
        $user = User::where('email', 'ayu.nuraini@puskesmas.go.id')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        // Di-redirect kembali ke faskes.dashboard dengan error message
        $response->assertRedirect('/faskes/dashboard');
        $response->assertSessionHas('error');
    }
}
