<?php
// database/migrations/..._create_jenis_cutis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cuti');
            $table->boolean('perlu_lampiran')->default(false);
            $table->timestamps();
        });

        // Langsung isi datanya
        DB::table('jenis_cuti')->insert([
            ['nama_cuti' => 'Cuti Tahunan', 'perlu_lampiran' => false],
            ['nama_cuti' => 'Cuti Melahirkan', 'perlu_lampiran' => true],
            ['nama_cuti' => 'Cuti Khusus', 'perlu_lampiran' => true],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};