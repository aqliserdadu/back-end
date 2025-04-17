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
        Schema::create('tbl_parameter', function (Blueprint $table) {
            $table->id();
            $table->integer('idsensor');
            $table->enum("tipe",array("GPIO","modbus"))->default("modbus");;
            $table->char('name');
            $table->char('post')->nullable();
            $table->char('parsing');
            $table->char('unit');
            $table->float("resolution")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_parameter');
    }
};
