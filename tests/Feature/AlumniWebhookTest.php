<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_student_can_view_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Katalog');
        $response->assertSee('Alumni MNI IPB');
    }

    public function test_gform_webhook_saves_data_successfully()
    {
        $payload = [
            'token' => 'mni_ipb_alumni_secret_key_2026',
            'Timestamp' => '8/15/2026 12:00:00',
            'Nama Lengkap' => 'Budi Santoso Test',
            'Nama Panggilan' => 'Budi',
            'Angkatan masuk kuliah (misal 57)' => '58',
            'Nomor HP / WA (format : 6281-xxxxxx)' => '081299998888',
            'Email pribadi' => 'budi.santoso@alumni.ipb.ac.id',
            'Kota domisili saat ini' => 'Bogor',
            'Bidang usaha atau industri saat ini' => 'Logistik & Warehousing',
            'Nama Perusahaan' => 'PT Logistics Express',
            'Jabatan atau posisi saat ini' => 'Logistics Manager',
            'Apakah bersedia jika suatu saat diundang menjadi dosen tamu atau mengisi sharing session?' => 'Bersedia',
            'Masukan atau saran untuk Program Studi MNI IPB University' => 'Pertahankan dan tingkatkan kualitas praktikum.'
        ];

        $response = $this->postJson('/api/gform-webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('alumnis', [
            'nama_lengkap' => 'Budi Santoso Test',
            'email_pribadi' => 'budi.santoso@alumni.ipb.ac.id',
            'angkatan' => '58',
            'kota_domisili' => 'Bogor',
            'bidang_industri' => 'Logistik & Warehousing',
            'nama_perusahaan' => 'PT Logistics Express',
            'jabatan_posisi' => 'Logistics Manager',
        ]);
    }

    public function test_gform_webhook_handles_alternate_keys_and_arrays()
    {
        // Sample payload with alternate headers & array values as sent by Apps Script e.namedValues
        $payload = [
            'token' => 'mni_ipb_alumni_secret_key_2026',
            'nama alumni' => ['Siti Rahmawati'],
            'angkatan masuk' => ['56'],
            'domisili' => ['Jakarta Selatan'],
            'bidang usaha' => ['Perbankan & Fintech'],
            'tempat kerja' => ['Bank Mandiri'],
            'karir' => ['Product Manager'],
            'kontak' => ['081399887766'],
            'email' => ['siti.rahma@gmail.com']
        ];

        $response = $this->postJson('/api/gform-webhook', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('alumnis', [
            'nama_lengkap' => 'Siti Rahmawati',
            'angkatan' => '56',
            'kota_domisili' => 'Jakarta Selatan',
            'bidang_industri' => 'Perbankan & Fintech',
            'nama_perusahaan' => 'Bank Mandiri',
            'jabatan_posisi' => 'Product Manager',
        ]);
    }

    public function test_gform_webhook_handles_combined_career_workplace()
    {
        $payload = [
            'token' => 'mni_ipb_alumni_secret_key_2026',
            'nama_lengkap' => 'Ahmad Fauzi',
            'domisili saat ini' => 'Bandung',
            'sektor industri' => 'E-Commerce',
            'karir & tempat kerja' => 'Senior Developer di Tokopedia',
            'angkatan' => '57'
        ];

        $response = $this->postJson('/api/gform-webhook', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('alumnis', [
            'nama_lengkap' => 'Ahmad Fauzi',
            'kota_domisili' => 'Bandung',
            'bidang_industri' => 'E-Commerce',
            'nama_perusahaan' => 'Tokopedia',
            'jabatan_posisi' => 'Senior Developer',
        ]);
    }
}
