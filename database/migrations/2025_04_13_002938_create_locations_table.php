<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_name');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->timestamps();
        });
    }
};
