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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->integer('max_quota');
            $table->integer('current_quota')->default(0);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->date('date');
            $table->string('photo');
            $table->integer('price');

            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
