<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->string('timestamp_gform')->nullable();
            $table->string('score')->nullable();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('angkatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('email_perusahaan')->nullable();
            $table->string('akun_sosmed')->nullable();
            $table->string('kota_domisili')->nullable();
            $table->string('bidang_industri')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan_posisi')->nullable();
            $table->string('bersedia_dosen_tamu')->nullable();
            $table->text('saran_prodi')->nullable();
            $table->string('gform_response_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnis');
    }
};
