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
        Schema::create('class_kuotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->integer('total_quota');
            $table->integer('quota_available');
            $table->timestamps();


            $table->foreign('class_id')->references('id')->on('class_rooms')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_kuotas');
    }
};
