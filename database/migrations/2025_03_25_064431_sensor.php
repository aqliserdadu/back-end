<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_sensor', function (Blueprint $table) {
            $table->id();
            $table->char("sensorname");
            $table->char("port");
            $table->char("baudrate");
            $table->char("slaveid");
            $table->char("functioncode");
            $table->char("databits");
            $table->char("stopbits");
            $table->char("partiy");
            $table->char("length");
            $table->char("address");
            $table->char("crc");
            $table->char("metode");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_daftarsensor');
    }
};
