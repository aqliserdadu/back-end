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
         #susunan insert database (ph,tss,nh3n,cod,depth,debit,rain,temperature,water)
         Schema::create('tbl_sensor_data', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->dateTime("dateall");
            $table->bigInteger("datetime");
            $table->enum("tipe",array("otomatis","manual"))->default("otomatis");
            $table->float("pH")->nullable();
            $table->float("tss")->nullable();
            $table->float("nh3n")->nullable();
            $table->float("cod")->nullable();
            $table->float("depth")->nullable();
            $table->float("debit")->nullable();
            $table->float("rainfall")->nullable();
            $table->float("temperature")->nullable();
            $table->float("waterpressure")->nullable();
            $table->timestamps(); // ini juga bisa nullable secara default
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sensor_data');
    }
};
