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
            $table->integer("baudrate");
            $table->integer("slaveid");
            $table->integer("functioncode");
            $table->integer("databits");
            $table->integer("stopbits");
            $table->integer("partiy");
            $table->integer("length");
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
