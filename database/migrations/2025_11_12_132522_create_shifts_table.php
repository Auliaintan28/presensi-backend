<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('shifts', function (Blueprint $table) {
        $table->id();
        $table->string('nama_shift'); // Misal: "Shift Pagi UGD"
        $table->time('jam_masuk'); // Misal: 07:00:00
        $table->time('jam_pulang'); // Misal: 14:00:00
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
        Schema::dropIfExists('shifts');
    }
}
