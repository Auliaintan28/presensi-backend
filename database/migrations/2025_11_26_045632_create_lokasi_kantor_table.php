<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLokasiKantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lokasi_kantor', function (Blueprint $table) {
        $table->id();
        $table->string('alamat')->nullable();
        $table->decimal('latitude', 11, 8); // Koordinat
        $table->decimal('longitude', 11, 8); // Koordinat
        $table->integer('radius_meter')->default(50); // Jarak toleransi absen
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
        Schema::dropIfExists('lokasi_kantor');
    }
}
