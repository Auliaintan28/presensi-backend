<?php
// database/migrations/..._create_pengajuan_cutis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();

            // Link ke pegawai yang mengajukan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Link ke jenis cuti yang dipilih
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan');

            // Path ke file lampiran di server, boleh null
            $table->string('file_lampiran')->nullable(); 
            
            // Status: diajukan, disetujui, ditolak
            $table->string('status', 50)->default('diajukan'); 
            
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};