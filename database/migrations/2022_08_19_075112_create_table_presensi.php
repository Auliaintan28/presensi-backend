<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePresensi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users') 
                  ->onDelete('cascade');
            $table->date('tanggal');

            // --- DATA MASUK ---
            $table->time('masuk');
            $table->decimal('latitude_datang', 12, 5);
            $table->decimal('longitude_datang', 12, 5);
            $table->boolean('is_terlambat')->default(false); // Status Datang

            // --- DATA PULANG (WAJIB NULLABLE) ---
            $table->time('pulang')->nullable(); // <-- INI PENTING
            $table->decimal('latitude_pulang', 12, 5)->nullable(); // <-- INI PENTING
            $table->decimal('longitude_pulang', 12, 5)->nullable(); // <-- INI PENTING
            $table->boolean('is_pulang_cepat')->default(false)->nullable(); // <-- INI PENTING

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
        Schema::dropIfExists('presensi'); 
    }
}
