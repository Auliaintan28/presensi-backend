<?php
// database/migrations/..._create_pengajuan_dinas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_dinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('tujuan'); // Field baru Anda
            $table->text('keterangan'); // Field baru Anda
            $table->string('file_lampiran'); // WAJIB, tidak boleh null
            $table->string('status', 50)->default('diajukan');
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_dinas');
    }
};