<?php

namespace Tests\Feature;

use App\Models\Faskes;
use App\Models\Inbox;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PBI-21: Feature tests untuk Manajemen Pesan Internal / Inbox Faskes (F22).
 * Menguji seluruh endpoint API dan halaman web sesuai spesifikasi Proposal.
 */
class InboxApiTest extends TestCase
{
    use RefreshDatabase;

    private User $userFaskes;

    private User $userDinkes;

    private User $userPenyedia;

    private User $userThirdParty;

    private Faskes $faskes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faskes = Faskes::create([
            'nama_faskes' => 'Puskesmas Sukamaju',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Sukamaju No. 18',
            'kota' => 'Kota Bandung',
        ]);

        $this->userFaskes = User::create([
            'name' => 'Ayu Nuraini',
            'email' => 'ayu.nuraini@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'faskes_id' => $this->faskes->id,
            'is_active' => true,
        ]);

        $this->userDinkes = User::create([
            'name' => 'Dr. Hendra Gunawan',
            'email' => 'dinkes@bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'dinkes',
            'is_active' => true,
        ]);

        $this->userPenyedia = User::create([
            'name' => 'Budi Santoso',
            'email' => 'penyedia@farmasikota.id',
            'password' => Hash::make('password123'),
            'role' => 'penyedia',
            'is_active' => true,
        ]);

        $this->userThirdParty = User::create([
            'name' => 'Pengguna Lain',
            'email' => 'lain@puskesmas.go.id',
            'password' => Hash::make('password123'),
            'role' => 'faskes',
            'is_active' => true,
        ]);
    }

    // ── 1. Autentikasi & Hak Akses Dasar ──────────────────────────────────

    #[Test]
    public function unauthenticated_user_cannot_access_inbox_api(): void
    {
        $response = $this->getJson('/api/inbox');
        $response->assertStatus(401);
    }

    #[Test]
    public function unauthenticated_user_cannot_send_message_via_api(): void
    {
        $response = $this->postJson('/api/inbox', [
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Tes Pesan',
            'isi' => 'Isi tes pesan',
        ]);
        $response->assertStatus(401);
    }

    // ── 2. READ (Melihat Daftar Inbox & Filter) ───────────────────────────

    #[Test]
    public function user_can_get_inbox_messages_and_only_sees_their_own(): void
    {
        // Pesan untuk userFaskes
        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan untuk Ayu',
            'isi' => 'Isi pesan untuk Ayu',
        ]);

        // Pesan untuk userThirdParty
        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userThirdParty->id,
            'subjek' => 'Pesan rahasia untuk pihak lain',
            'isi' => 'Tidak boleh terlihat oleh Ayu',
        ]);

        $response = $this->actingAs($this->userFaskes)->getJson('/api/inbox');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'folder' => 'inbox',
                    'total' => 1,
                ],
            ])
            ->assertJsonFragment(['subjek' => 'Pesan untuk Ayu'])
            ->assertJsonMissing(['subjek' => 'Pesan rahasia untuk pihak lain']);
    }

    #[Test]
    public function user_can_filter_unread_and_read_messages(): void
    {
        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Belum Dibaca',
            'isi' => 'Isi unread',
            'is_read' => false,
        ]);

        Inbox::create([
            'pengirim_id' => $this->userPenyedia->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Sudah Dibaca',
            'isi' => 'Isi read',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $responseUnread = $this->actingAs($this->userFaskes)->getJson('/api/inbox?status=unread');
        $responseUnread->assertStatus(200)
            ->assertJsonFragment(['subjek' => 'Pesan Belum Dibaca'])
            ->assertJsonMissing(['subjek' => 'Pesan Sudah Dibaca']);

        $responseRead = $this->actingAs($this->userFaskes)->getJson('/api/inbox?status=read');
        $responseRead->assertStatus(200)
            ->assertJsonFragment(['subjek' => 'Pesan Sudah Dibaca'])
            ->assertJsonMissing(['subjek' => 'Pesan Belum Dibaca']);
    }

    #[Test]
    public function user_can_view_sent_messages_folder(): void
    {
        Inbox::create([
            'pengirim_id' => $this->userFaskes->id,
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Laporan Faskes Keluar',
            'isi' => 'Terkirim dari faskes',
        ]);

        $response = $this->actingAs($this->userFaskes)->getJson('/api/inbox?folder=sent');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'folder' => 'sent',
                    'total' => 1,
                ],
            ])
            ->assertJsonFragment(['subjek' => 'Laporan Faskes Keluar']);
    }

    #[Test]
    public function user_can_search_messages_by_keyword(): void
    {
        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Alokasi Serum Anti Bisa Ular',
            'isi' => 'Detail serum ABU',
        ]);

        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Jadwal Rapat Sosialisasi',
            'isi' => 'Undangan rapat',
        ]);

        $response = $this->actingAs($this->userFaskes)->getJson('/api/inbox?search=Bisa+Ular');

        $response->assertStatus(200)
            ->assertJsonFragment(['subjek' => 'Alokasi Serum Anti Bisa Ular'])
            ->assertJsonMissing(['subjek' => 'Jadwal Rapat Sosialisasi']);
    }

    #[Test]
    public function user_can_get_list_of_available_recipients_excluding_self(): void
    {
        $response = $this->actingAs($this->userFaskes)->getJson('/api/inbox/recipients');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonFragment(['email' => 'dinkes@bandung.go.id'])
            ->assertJsonFragment(['email' => 'penyedia@farmasikota.id'])
            ->assertJsonMissing(['email' => 'ayu.nuraini@puskesmas.go.id']);
    }

    // ── 3. CREATE (Mengirim Pesan Baru) ───────────────────────────────────

    #[Test]
    public function user_can_send_message_successfully(): void
    {
        $payload = [
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Permohonan Tambahan Infus RL',
            'isi' => 'Stok cairan infus Ringer Lactate tersisa 10 botol. Mohon bantuan pasokan.',
        ];

        $response = $this->actingAs($this->userFaskes)->postJson('/api/inbox', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim.',
                'data' => [
                    'pengirim_id' => $this->userFaskes->id,
                    'penerima_id' => $this->userDinkes->id,
                    'subjek' => 'Permohonan Tambahan Infus RL',
                    'is_read' => false,
                ],
            ]);

        $this->assertDatabaseHas('inbox', [
            'pengirim_id' => $this->userFaskes->id,
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Permohonan Tambahan Infus RL',
            'is_read' => 0,
        ]);
    }

    #[Test]
    public function user_cannot_send_message_to_themselves(): void
    {
        $payload = [
            'penerima_id' => $this->userFaskes->id, // Mengirim ke diri sendiri
            'subjek' => 'Tes Diri Sendiri',
            'isi' => 'Isi tes',
        ];

        $response = $this->actingAs($this->userFaskes)->postJson('/api/inbox', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['penerima_id']);
    }

    #[Test]
    public function send_message_fails_with_invalid_penerima_id(): void
    {
        $payload = [
            'penerima_id' => 99999, // ID tidak ada
            'subjek' => 'Tes Tujuan Salah',
            'isi' => 'Isi tes',
        ];

        $response = $this->actingAs($this->userFaskes)->postJson('/api/inbox', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['penerima_id']);
    }

    #[Test]
    public function send_message_fails_with_missing_subjek_and_isi(): void
    {
        $response = $this->actingAs($this->userFaskes)->postJson('/api/inbox', [
            'penerima_id' => $this->userDinkes->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subjek', 'isi']);
    }

    // ── 4. SHOW (Detail Pesan & Auto Mark Read) ───────────────────────────

    #[Test]
    public function user_can_view_single_message_detail_and_auto_marks_as_read(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Konfirmasi Alokasi',
            'isi' => 'Detail surat alokasi obat',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->userFaskes)->getJson("/api/inbox/{$msg->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $msg->id,
                    'subjek' => 'Konfirmasi Alokasi',
                    'is_read' => true,
                ],
            ]);

        $this->assertDatabaseHas('inbox', [
            'id' => $msg->id,
            'is_read' => 1,
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_view_someone_elses_message(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Rahasia',
            'isi' => 'Hanya untuk Ayu',
        ]);

        $response = $this->actingAs($this->userThirdParty)->getJson("/api/inbox/{$msg->id}");
        $response->assertStatus(403);
    }

    // ── 5. UPDATE (Tandai Read / Unread) ──────────────────────────────────

    #[Test]
    public function receiver_can_mark_message_as_read_and_unread(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Status Baca Tes',
            'isi' => 'Isi pesan',
            'is_read' => false,
        ]);

        // Tandai sudah dibaca
        $response1 = $this->actingAs($this->userFaskes)->putJson("/api/inbox/{$msg->id}", [
            'is_read' => true,
        ]);

        $response1->assertStatus(200)
            ->assertJsonFragment(['is_read' => true]);
        $this->assertDatabaseHas('inbox', ['id' => $msg->id, 'is_read' => 1]);

        // Tandai kembali belum dibaca
        $response2 = $this->actingAs($this->userFaskes)->putJson("/api/inbox/{$msg->id}", [
            'is_read' => false,
        ]);

        $response2->assertStatus(200)
            ->assertJsonFragment(['is_read' => false]);
        $this->assertDatabaseHas('inbox', ['id' => $msg->id, 'is_read' => 0]);
    }

    #[Test]
    public function sender_cannot_modify_read_status_of_message(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userFaskes->id,
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Pesan Terkirim',
            'isi' => 'Isi pesan',
            'is_read' => false,
        ]);

        // Pengirim mencoba menandai read/unread untuk penerima
        $response = $this->actingAs($this->userFaskes)->putJson("/api/inbox/{$msg->id}", [
            'is_read' => true,
        ]);

        $response->assertStatus(403);
    }

    // ── 6. DELETE (Hapus Pesan) ───────────────────────────────────────────

    #[Test]
    public function user_can_delete_message_for_their_inbox(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Akan Dihapus',
            'isi' => 'Isi pesan',
        ]);

        $response = $this->actingAs($this->userFaskes)->deleteJson("/api/inbox/{$msg->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pesan berhasil dihapus dari kotak pesan Anda.',
            ]);

        // Pesan tidak lagi muncul di kotak masuk penerima
        $listResponse = $this->actingAs($this->userFaskes)->getJson('/api/inbox');
        $listResponse->assertJsonMissing(['subjek' => 'Pesan Akan Dihapus']);

        // Tetapi masih ada di database (hanya ditandai deleted_by_penerima)
        $this->assertDatabaseHas('inbox', [
            'id' => $msg->id,
            'deleted_by_penerima' => 1,
            'deleted_by_pengirim' => 0,
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_delete_someone_elses_message(): void
    {
        $msg = Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Penting',
            'isi' => 'Isi pesan',
        ]);

        $response = $this->actingAs($this->userThirdParty)->deleteJson("/api/inbox/{$msg->id}");
        $response->assertStatus(403);
    }

    #[Test]
    public function delete_returns_404_for_nonexistent_message(): void
    {
        $response = $this->actingAs($this->userFaskes)->deleteJson('/api/inbox/99999');
        $response->assertStatus(404);
    }

    // ── 7. Web Interface (Blade Views & Form Submission) ──────────────────

    #[Test]
    public function web_inbox_page_can_be_rendered(): void
    {
        Inbox::create([
            'pengirim_id' => $this->userDinkes->id,
            'penerima_id' => $this->userFaskes->id,
            'subjek' => 'Pesan Web Rendering',
            'isi' => 'Tes tampilan halaman web',
        ]);

        $response = $this->actingAs($this->userFaskes)->get('/inbox');

        $response->assertStatus(200);
        $response->assertSee('Kotak Pesan');
        $response->assertSee('Tulis Pesan Baru');
        $response->assertSee('Pesan Web Rendering');
    }

    #[Test]
    public function web_store_inbox_redirects_with_success_message(): void
    {
        $payload = [
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Kirim via Web Form',
            'isi' => 'Isi pesan dari web form blade',
        ];

        $response = $this->actingAs($this->userFaskes)->post('/inbox', $payload);

        $response->assertRedirect('/inbox?folder=sent');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inbox', [
            'pengirim_id' => $this->userFaskes->id,
            'penerima_id' => $this->userDinkes->id,
            'subjek' => 'Kirim via Web Form',
        ]);
    }
}
