<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalKerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_kerja', function (Blueprint $table) {
            $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('shift_id')->constrained('shifts');
        $table->date('tanggal');
        $table->timestamps();

        // Pastikan 1 pegawai hanya punya 1 shift per hari
        $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_kerja');
    }
}
