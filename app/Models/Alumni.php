<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumnis';

    protected $fillable = [
        'timestamp_gform',
        'score',
        'nama_lengkap',
        'nama_panggilan',
        'angkatan',
        'no_hp',
        'email_pribadi',
        'email_perusahaan',
        'akun_sosmed',
        'kota_domisili',
        'bidang_industri',
        'nama_perusahaan',
        'jabatan_posisi',
        'bersedia_dosen_tamu',
        'saran_prodi',
        'gform_response_id',
    ];

    /**
     * Helper scope to filter alumni by search term (nama, email, perusahaan, kota)
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nama_panggilan', 'like', "%{$search}%")
              ->orWhere('email_pribadi', 'like', "%{$search}%")
              ->orWhere('email_perusahaan', 'like', "%{$search}%")
              ->orWhere('nama_perusahaan', 'like', "%{$search}%")
              ->orWhere('kota_domisili', 'like', "%{$search}%")
              ->orWhere('bidang_industri', 'like', "%{$search}%")
              ->orWhere('jabatan_posisi', 'like', "%{$search}%");
        });
    }

    /**
     * Clean and normalize angkatan string (e.g. "57", "58")
     */
    public function getAngkatanFormattedAttribute()
    {
        if (preg_match('/(\d+)/', $this->angkatan, $matches)) {
            return 'Angkatan ' . $matches[1];
        }
        return $this->angkatan ?: 'Tidak Diketahui';
    }
}
