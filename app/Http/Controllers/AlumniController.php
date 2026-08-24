<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AlumniController extends Controller
{
    /**
     * Dashboard & Alumni Data Table View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $angkatan = $request->input('angkatan');
        $statusDosen = $request->input('bersedia_dosen');
        $bidang = $request->input('bidang');

        $query = Alumni::query()->search($search);

        if (!empty($angkatan)) {
            $query->where('angkatan', 'like', "%{$angkatan}%");
        }

        if (!empty($statusDosen)) {
            $query->where('bersedia_dosen_tamu', $statusDosen);
        }

        if (!empty($bidang)) {
            $query->where('bidang_industri', 'like', "%{$bidang}%");
        }

        // Urutkan data berdasarkan Angkatan (43 -> 59) lalu nama lengkap
        $alumnis = $query->orderByRaw('CASE WHEN CAST(angkatan AS UNSIGNED) = 0 THEN 999 ELSE CAST(angkatan AS UNSIGNED) END ASC')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Calculate Stats
        $totalAlumni = Alumni::count();
        $totalBersediaDosen = Alumni::where('bersedia_dosen_tamu', 'Bersedia')->count();
        
        // List angkatan lengkap dari 43 sampai 59 tanpa ada yang terlewat / terskip
        $listAngkatan = range(43, 59);

        $listBidang = Alumni::select('bidang_industri')
            ->whereNotNull('bidang_industri')
            ->where('bidang_industri', '!=', '-')
            ->distinct()
            ->limit(20)
            ->pluck('bidang_industri');

        return view('alumni.index', compact(
            'alumnis',
            'totalAlumni',
            'totalBersediaDosen',
            'listAngkatan',
            'listBidang',
            'search',
            'angkatan',
            'statusDosen',
            'bidang'
        ));
    }

    /**
     * Get single alumni detail via AJAX/Modal
     */
    public function show($id)
    {
        $alumni = Alumni::findOrFail($id);
        return response()->json($alumni);
    }

    /**
     * Handle manual CSV / Excel file import
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:15360',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $content = file_get_contents($path);
        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return redirect()->route('alumni.index')->with('error', 'File CSV kosong.');
        }

        // Auto-detect delimiter from first line
        $firstLine = $lines[0];
        $delimiters = [",", "\t", ";"];
        $detectedDelimiter = ",";
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delim;
            }
        }

        $rows = array_map(function($line) use ($detectedDelimiter) {
            return str_getcsv($line, $detectedDelimiter);
        }, $lines);

        $header = array_shift($rows);
        $importedCount = 0;

        // Try to find index by header names
        $headerMap = [];
        foreach ($header as $idx => $colName) {
            $cleanName = strtolower(trim($colName));
            if (str_contains($cleanName, 'nama lengkap')) $headerMap['nama_lengkap'] = $idx;
            elseif (str_contains($cleanName, 'nama panggilan')) $headerMap['nama_panggilan'] = $idx;
            elseif (str_contains($cleanName, 'angkatan')) $headerMap['angkatan'] = $idx;
            elseif (str_contains($cleanName, 'nomor hp') || str_contains($cleanName, 'no hp') || str_contains($cleanName, 'wa')) $headerMap['no_hp'] = $idx;
            elseif (str_contains($cleanName, 'email pribadi') || ($cleanName === 'email')) $headerMap['email_pribadi'] = $idx;
            elseif (str_contains($cleanName, 'email perusahaan')) $headerMap['email_perusahaan'] = $idx;
            elseif (str_contains($cleanName, 'sosmed') || str_contains($cleanName, 'media sosial')) $headerMap['akun_sosmed'] = $idx;
            elseif (str_contains($cleanName, 'domisili') || str_contains($cleanName, 'kota')) $headerMap['kota_domisili'] = $idx;
            elseif (str_contains($cleanName, 'bidang')) $headerMap['bidang_industri'] = $idx;
            elseif (str_contains($cleanName, 'nama perusahaan') || str_contains($cleanName, 'perusahaan')) $headerMap['nama_perusahaan'] = $idx;
            elseif (str_contains($cleanName, 'jabatan') || str_contains($cleanName, 'posisi')) $headerMap['jabatan_posisi'] = $idx;
            elseif (str_contains($cleanName, 'dosen') || str_contains($cleanName, 'sharing')) $headerMap['bersedia_dosen_tamu'] = $idx;
            elseif (str_contains($cleanName, 'saran') || str_contains($cleanName, 'masukan')) $headerMap['saran_prodi'] = $idx;
            elseif (str_contains($cleanName, 'timestamp')) $headerMap['timestamp_gform'] = $idx;
        }

        foreach ($rows as $row) {
            if (empty($row) || count($row) < 2) continue;

            $nama = isset($headerMap['nama_lengkap']) ? ($row[$headerMap['nama_lengkap']] ?? null) : ($row[2] ?? ($row[0] ?? null));
            if (!$nama || trim($nama) == 'Nama Lengkap' || trim($nama) == '') continue;

            $emailPribadi = isset($headerMap['email_pribadi']) ? ($row[$headerMap['email_pribadi']] ?? null) : ($row[6] ?? null);

            Alumni::updateOrCreate(
                [
                    'nama_lengkap' => trim($nama),
                    'email_pribadi' => $emailPribadi ? trim($emailPribadi) : null
                ],
                [
                    'timestamp_gform' => isset($headerMap['timestamp_gform']) ? ($row[$headerMap['timestamp_gform']] ?? null) : ($row[0] ?? now()->toDateTimeString()),
                    'score' => $row[1] ?? null,
                    'nama_lengkap' => trim($nama),
                    'nama_panggilan' => isset($headerMap['nama_panggilan']) ? ($row[$headerMap['nama_panggilan']] ?? null) : ($row[3] ?? null),
                    'angkatan' => isset($headerMap['angkatan']) ? ($row[$headerMap['angkatan']] ?? null) : ($row[4] ?? null),
                    'no_hp' => isset($headerMap['no_hp']) ? ($row[$headerMap['no_hp']] ?? null) : ($row[5] ?? null),
                    'email_pribadi' => $emailPribadi ? trim($emailPribadi) : null,
                    'email_perusahaan' => isset($headerMap['email_perusahaan']) ? ($row[$headerMap['email_perusahaan']] ?? null) : ($row[7] ?? null),
                    'akun_sosmed' => isset($headerMap['akun_sosmed']) ? ($row[$headerMap['akun_sosmed']] ?? null) : ($row[8] ?? null),
                    'kota_domisili' => isset($headerMap['kota_domisili']) ? ($row[$headerMap['kota_domisili']] ?? null) : ($row[9] ?? null),
                    'bidang_industri' => isset($headerMap['bidang_industri']) ? ($row[$headerMap['bidang_industri']] ?? null) : ($row[10] ?? null),
                    'nama_perusahaan' => isset($headerMap['nama_perusahaan']) ? ($row[$headerMap['nama_perusahaan']] ?? null) : ($row[11] ?? null),
                    'jabatan_posisi' => isset($headerMap['jabatan_posisi']) ? ($row[$headerMap['jabatan_posisi']] ?? null) : ($row[12] ?? null),
                    'bersedia_dosen_tamu' => isset($headerMap['bersedia_dosen_tamu']) ? ($row[$headerMap['bersedia_dosen_tamu']] ?? null) : ($row[13] ?? null),
                    'saran_prodi' => isset($headerMap['saran_prodi']) ? ($row[$headerMap['saran_prodi']] ?? null) : ($row[14] ?? null),
                ]
            );
            $importedCount++;
        }

        return redirect()->route('alumni.index')->with('success', "Berhasil menyinkronkan & mengimpor {$importedCount} data alumni dari file spreadsheet!");
    }

    /**
     * Export Alumni Data to CSV
     */
    public function exportCsv()
    {
        $alumnis = Alumni::all();
        $filename = "data_alumni_mni_ipb_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Timestamp', 'Nama Lengkap', 'Nama Panggilan', 'Angkatan',
            'No HP/WA', 'Email Pribadi', 'Email Perusahaan', 'Akun Sosmed',
            'Kota Domisili', 'Bidang Industri', 'Nama Perusahaan', 'Jabatan/Posisi',
            'Bersedia Sharing/Dosen Tamu', 'Saran untuk MNI IPB'
        ];

        $callback = function() use($alumnis, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, $columns);

            foreach ($alumnis as $a) {
                fputcsv($file, [
                    $a->id,
                    $a->timestamp_gform,
                    $a->nama_lengkap,
                    $a->nama_panggilan,
                    $a->angkatan,
                    $a->no_hp,
                    $a->email_pribadi,
                    $a->email_perusahaan,
                    $a->akun_sosmed,
                    $a->kota_domisili,
                    $a->bidang_industri,
                    $a->nama_perusahaan,
                    $a->jabatan_posisi,
                    $a->bersedia_dosen_tamu,
                    $a->saran_prodi,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Integration guide page with Apps Script setup
     */
    public function integration()
    {
        $webhookUrl = route('api.gform_webhook');
        $secretToken = env('GFORM_WEBHOOK_SECRET', 'mni_ipb_alumni_secret_key_2026');

        return view('alumni.integration', compact('webhookUrl', 'secretToken'));
    }

    /**
     * Delete alumni record
     */
    public function destroy($id)
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->delete();

        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil dihapus!');
    }
}
