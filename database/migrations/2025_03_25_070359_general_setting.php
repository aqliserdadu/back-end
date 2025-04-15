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
        Schema::create('tbl_setting', function (Blueprint $table) {
            $table->id();
            $table->integer("interval")->nullable();
            $table->char("email")->nullable();
            $table->integer("automeasure")->nullable();

            $table->char("deviceid")->nullable();
            $table->char("stationname")->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->char("klhapi")->nullable();
            $table->char("klhtoken")->nullable();
            $table->integer("klhstatus")->nullable();
            $table->char("wqmsapi")->nullable();
            $table->char("wqmstoken")->nullable();
            $table->integer("wqmsstatus")->nullable();

            $table->char("stmpserver")->nullable();
            $table->integer("stmpport")->nullable();
            $table->char("stmpusername")->nullable();
            $table->char("stmppassword")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_setting');
    }
};
