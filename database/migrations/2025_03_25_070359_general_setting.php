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
            $table->integer("interval")->default(0);
            $table->char("email")->nullable();
            $table->enum("automeasure",array(0,1))->default(0);

            $table->char("deviceid")->nullable();
            $table->char("stationname")->nullable();
            $table->decimal('latitude', 10, 7)->default(0);
            $table->decimal('longitude', 10, 7)->default(0);

            $table->char("klhapi")->nullable();
            $table->char("klhtoken")->nullable();
            $table->integer("klhstatus")->default(0);
            $table->char("wqmsapi")->nullable();
            $table->char("wqmstoken")->nullable();
            $table->integer("wqmsstatus")->default(0);

            $table->char("stmpserver")->nullable();
            $table->integer("stmpport")->default(22);
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
