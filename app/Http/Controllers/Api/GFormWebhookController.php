<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GFormWebhookController extends Controller
{
    /**
     * Handle incoming Google Form / Google Sheets webhook payload (Single & Bulk)
     */
    public function handle(Request $request)
    {
        $secret = env('GFORM_WEBHOOK_SECRET', 'mni_ipb_alumni_secret_key_2026');
        $tokenPassed = $request->header('X-Webhook-Secret') 
                     ?? $request->input('secret_token') 
                     ?? $request->input('token');

        if ($tokenPassed !== $secret) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid secret token'
            ], 401);
        }

        $allData = $request->all();

        // 1. Bulk Sync Mode: payload contains 'rows' array
        if (isset($allData['rows']) && is_array($allData['rows'])) {
            $count = 0;
            DB::transaction(function() use ($allData, &$count) {
                foreach ($allData['rows'] as $row) {
                    if (is_array($row) && !empty($row)) {
                        $saved = $this->processSingleRow($row);
                        if ($saved) {
                            $count++;
                        }
                    }
                }
            });

            Log::info("GForm Bulk Sync Completed. {$count} alumni updated/created.");

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil menyinkronkan {$count} data alumni ke database!",
                'count' => $count
            ], 200);
        }

        // 2. Single Record Mode (Default onFormSubmit)
        Log::info('GForm Single Webhook Received:', $allData);
        $alumni = $this->processSingleRow($allData);

        if (!$alumni) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error: Nama Lengkap is required'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data alumni MNI IPB berhasil diperbarui / ditambahkan!',
            'data' => $alumni
        ], 200);
    }

    /**
     * Process and upsert single row data from Google Form / Sheets
     */
    public function processSingleRow(array $payload)
    {
        // Normalize payload: unwrap array values (Google Apps Script e.namedValues sends arrays)
        $flat = [];
        foreach ($payload as $k => $v) {
            if (is_array($v)) {
                $flat[$k] = trim(implode(', ', array_filter($v, fn($item) => $item !== null && $item !== '')));
            } else {
                $flat[$k] = is_string($v) ? trim($v) : $v;
            }
        }

        // Helper to find value by checking multiple pattern candidates
        $findValue = function(array $patterns, array $excludePatterns = []) use ($flat) {
            // 1. Check exact matches first
            foreach ($patterns as $p) {
                if (isset($flat[$p]) && $flat[$p] !== '' && $flat[$p] !== null) {
                    return (string)$flat[$p];
                }
            }
            // 2. Check fuzzy matches across all keys
            foreach ($flat as $key => $val) {
                if ($val === '' || $val === null) continue;
                $cleanKey = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $key)));
                
                // Check exclusions
                $excluded = false;
                foreach ($excludePatterns as $ex) {
                    if (str_contains($cleanKey, strtolower($ex))) {
                        $excluded = true;
                        break;
                    }
                }
                if ($excluded) continue;

                foreach ($patterns as $p) {
                    $cleanPattern = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $p)));
                    if (str_contains($cleanKey, $cleanPattern)) {
                        return (string)$val;
                    }
                }
            }
            return null;
        };

        // Extract Nama Lengkap
        $namaLengkap = $findValue(
            ['nama_lengkap', 'Nama Lengkap', 'nama lengkap', 'nama alumni', 'full name', 'nama'],
            ['perusahaan', 'kantor', 'instansi', 'panggilan', 'nickname']
        );

        if (!$namaLengkap) {
            return null;
        }

        // Extract Email Pribadi & Kantor
        $emailPerusahaan = $findValue(
            ['email_perusahaan', 'Email perusahaan', 'email kantor', 'corporate email', 'work email', 'email instansi']
        );

        $emailPribadi = $findValue(
            ['email_pribadi', 'Email pribadi', 'email personal', 'personal email', 'email'],
            ['perusahaan', 'kantor', 'instansi', 'corporate']
        );

        // Extract Angkatan
        $angkatan = $findValue(
            ['angkatan', 'Angkatan masuk kuliah (misal 57)', 'angkatan masuk', 'tahun masuk', 'batch', 'class of']
        );
        if ($angkatan && preg_match('/(\d+)/', $angkatan, $m)) {
            $angkatan = $m[1];
        }

        // Extract Nama Panggilan
        $namaPanggilan = $findValue(
            ['nama_panggilan', 'Nama Panggilan', 'panggilan', 'nickname']
        );

        // Extract No HP / WhatsApp
        $noHp = $findValue(
            ['no_hp', 'Nomor HP / WA (format : 6281-xxxxxx)', 'Nomor HP / WA', 'no wa', 'nomor whatsapp', 'nomor hp', 'telepon', 'telp', 'phone', 'whatsapp', 'kontak']
        );

        // Extract Media Sosial
        $akunSosmed = $findValue(
            ['akun_sosmed', 'Akun Media Sosial', 'media sosial', 'sosmed', 'social media', 'instagram', 'tiktok', 'linkedin', 'threads']
        );

        // Extract Domisili (Kota Domisili)
        $kotaDomisili = $findValue(
            ['kota_domisili', 'Kota domisili saat ini', 'kota domisili', 'domisili saat ini', 'domisili', 'tempat tinggal', 'lokasi saat ini', 'kabupaten kota domisili', 'kota asal domisili', 'kota', 'kabupaten', 'residence', 'city']
        );

        // Extract Bidang Industri / Usaha
        $bidangIndustri = $findValue(
            ['bidang_industri', 'Bidang usaha atau industri saat ini', 'bidang usaha', 'bidang industri', 'bidang usaha industri', 'sektor usaha', 'sektor industri', 'bidang bisnis', 'bidang pekerjaan', 'industri', 'bidang', 'industry']
        );

        // Check for combined Career & Workplace field first
        $combinedCareerWorkplace = $findValue([
            'karir & tempat kerja', 'karir dan tempat kerja', 'karir tempat kerja',
            'pekerjaan & tempat kerja', 'pekerjaan dan instansi', 'pekerjaan & kantor',
            'posisi & perusahaan', 'jabatan & instansi'
        ]);

        $splitJabatan = null;
        $splitPerusahaan = null;
        if ($combinedCareerWorkplace) {
            if (preg_match('/^(.*?)(?:\s+(?:di|at|@)\s+|\s*[-–—|]\s*)(.+)$/iu', $combinedCareerWorkplace, $matches)) {
                $splitJabatan = trim($matches[1]);
                $splitPerusahaan = trim($matches[2]);
            } else {
                $splitJabatan = $combinedCareerWorkplace;
                $splitPerusahaan = $combinedCareerWorkplace;
            }
        }

        // Extract Nama Perusahaan / Tempat Kerja
        $namaPerusahaan = $findValue(
            ['nama_perusahaan', 'Nama Perusahaan', 'tempat kerja', 'tempat bekerja', 'nama kantor', 'nama instansi', 'nama institusi', 'perusahaan', 'instansi', 'lembaga', 'institusi', 'company', 'kantor', 'workplace'],
            ['email', 'jabatan', 'posisi', 'role', 'karir', 'pekerjaan', 'profesi']
        ) ?? $splitPerusahaan;

        // Extract Jabatan / Posisi / Karir
        $jabatanPosisi = $findValue(
            ['jabatan_posisi', 'Jabatan atau posisi saat ini', 'jabatan posisi', 'posisi saat ini', 'jabatan saat ini', 'posisi jabatan', 'jabatan', 'posisi', 'karir', 'profesi', 'pekerjaan', 'job title', 'role', 'title', 'position'],
            ['email', 'perusahaan', 'instansi', 'lembaga', 'kantor', 'tempat kerja', 'tempat bekerja']
        ) ?? $splitJabatan;

        // If one of them still contains "di [Perusahaan]" (e.g. "Software Engineer di Google" in jabatan)
        if ($jabatanPosisi && !$namaPerusahaan && preg_match('/^(.*?)(?:\s+(?:di|at|@)\s+|\s*[-–—|]\s*)(.+)$/iu', $jabatanPosisi, $matches)) {
            $jabatanPosisi = trim($matches[1]);
            $namaPerusahaan = trim($matches[2]);
        }

        // Extract Bersedia Dosen Tamu / Sharing
        $bersediaDosen = $findValue(
            ['bersedia_dosen_tamu', 'Apakah bersedia jika suatu saat diundang menjadi dosen tamu atau mengisi sharing session?', 'dosen tamu', 'sharing session', 'dosen', 'sharing', 'narasumber', 'pemateri', 'guest lecturer']
        );
        if ($bersediaDosen) {
            if (stripos($bersediaDosen, 'tidak') !== false || stripos($bersediaDosen, 'belum') !== false) {
                $bersediaDosen = 'Tidak Bersedia';
            } elseif (stripos($bersediaDosen, 'ya') !== false || stripos($bersediaDosen, 'bersedia') !== false || stripos($bersediaDosen, 'bisa') !== false) {
                $bersediaDosen = 'Bersedia';
            }
        }

        // Extract Saran / Masukan
        $saranProdi = $findValue(
            ['saran_prodi', 'Masukan atau saran untuk Program Studi MNI IPB University', 'masukan atau saran', 'saran untuk prodi', 'masukan', 'saran', 'feedback', 'kritik']
        );

        $timestamp = $findValue(['timestamp', 'Timestamp', 'waktu']) ?? now()->toDateTimeString();
        $score = $findValue(['score', 'Score']) ?? null;
        $responseId = $findValue(['response_id', 'gform_response_id', 'id']) ?? null;

        return Alumni::updateOrCreate(
            [
                'nama_lengkap' => trim($namaLengkap),
                'email_pribadi' => $emailPribadi ? trim($emailPribadi) : null
            ],
            [
                'timestamp_gform' => $timestamp,
                'score' => $score,
                'nama_lengkap' => trim($namaLengkap),
                'nama_panggilan' => $namaPanggilan,
                'angkatan' => $angkatan,
                'no_hp' => $noHp,
                'email_pribadi' => $emailPribadi,
                'email_perusahaan' => $emailPerusahaan,
                'akun_sosmed' => $akunSosmed,
                'kota_domisili' => $kotaDomisili,
                'bidang_industri' => $bidangIndustri,
                'nama_perusahaan' => $namaPerusahaan,
                'jabatan_posisi' => $jabatanPosisi,
                'bersedia_dosen_tamu' => $bersediaDosen,
                'saran_prodi' => $saranProdi,
                'gform_response_id' => $responseId,
            ]
        );
    }
}
