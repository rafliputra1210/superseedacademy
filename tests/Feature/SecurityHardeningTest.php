<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Athlete;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Tes Proteksi Otentikasi: Tamu tidak boleh mengakses admin atau portal wali.
     */
    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect(route('login'));

        $responseWali = $this->get('/portal-wali/dashboard');
        $responseWali->assertRedirect(route('login'));
    }

    /**
     * 2. Tes Proteksi Otorisasi Fail-Closed pada RoleMiddleware:
     * User dengan role tidak berizin (misal: 'coach') ditolak dengan HTTP 403 Forbidden.
     */
    public function test_role_middleware_fails_closed_with_403_for_unauthorized_role(): void
    {
        $user = User::create([
            'name'     => 'Coach Test',
            'username' => 'coach_test',
            'password' => Hash::make('password123'),
            'role'     => 'wali_murid', // create initially
        ]);
        
        // Simulasikan role kustom/lain di luar admin & wali_murid
        $user->role = 'coach';

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * 3. Tes Proteksi Reset Password pada WaliManagerController:
     * Percobaan mereset password akun admin HARUS DITOLAK.
     */
    public function test_reset_password_rejects_admin_user(): void
    {
        $admin = User::create([
            'name'     => 'Admin Utama',
            'username' => 'admin_super',
            'password' => Hash::make('SecretAdminPass2026!'),
            'role'     => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin/manajemen-akun-wali')
            ->post(route('admin.wali.reset', $admin));

        $response->assertRedirect('/admin/manajemen-akun-wali');
        $response->assertSessionHas('error', 'Akses ditolak: Hanya akun wali murid yang dapat direset melalui fitur ini!');

        // Pastikan password admin tidak berubah
        $admin->refresh();
        $this->assertTrue(Hash::check('SecretAdminPass2026!', $admin->password));
    }

    /**
     * 4. Tes Fitur Mandiri Ubah Password Wali Murid:
     * Validasi current password dan pembaruan password baru.
     */
    public function test_wali_murid_can_update_password_with_valid_credentials(): void
    {
        $wali = User::create([
            'name'     => 'Orang Tua Murid',
            'username' => 'ortu_davi',
            'password' => Hash::make('superseed123'),
            'role'     => 'wali_murid',
        ]);

        // Coba dengan current password salah
        $failResponse = $this->actingAs($wali)->post(route('wali.profile.update-password'), [
            'current_password'      => 'wrongpassword',
            'password'              => 'NewSecurePass2026!',
            'password_confirmation' => 'NewSecurePass2026!',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // Coba dengan data valid
        $successResponse = $this->actingAs($wali)->post(route('wali.profile.update-password'), [
            'current_password'      => 'superseed123',
            'password'              => 'NewSecurePass2026!',
            'password_confirmation' => 'NewSecurePass2026!',
        ]);

        $successResponse->assertRedirect(route('wali.profile'));
        $successResponse->assertSessionHas('success');

        $wali->refresh();
        $this->assertTrue(Hash::check('NewSecurePass2026!', $wali->password));
    }

    /**
     * 5. Tes Rate Limiting pada Rute Komentar Publik:
     * Request ke-6 dalam 1 menit harus menghasilkan HTTP 429 Too Many Requests.
     */
    public function test_comment_route_rate_limiting(): void
    {
        $news = News::create([
            'judul'     => 'Latihan Perdana 2026',
            'slug'      => 'latihan-perdana-2026',
            'kategori'  => 'Kegiatan',
            'konten'    => 'Latihan perdana akademi berjalan lancar.',
            'tanggal'   => now()->toDateString(),
            'is_active' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $res = $this->post(route('landing.news.comment', $news->slug), [
                'nama'     => 'Pengunjung',
                'komentar' => 'Komentar ke-' . $i,
            ]);
            $res->assertStatus(302); // Redirect back
        }

        // Request ke-6 harus diblokir oleh throttle
        $blocked = $this->post(route('landing.news.comment', $news->slug), [
            'nama'     => 'Pengunjung Spammer',
            'komentar' => 'Spam komentar',
        ]);

        $blocked->assertStatus(429);
    }
}
