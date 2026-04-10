<?php
// database/migrations/..._create_pengajuan_izin_jams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_izin_jam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_izin');
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable(); 
            $table->time('jam_selesai')->nullable();
            $table->text('keterangan');
            $table->string('file_lampiran')->nullable();
            $table->string('status', 50)->default('diajukan');
             $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin_jam');
    }
};