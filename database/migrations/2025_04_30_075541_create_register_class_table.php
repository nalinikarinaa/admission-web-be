<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_class', function (Blueprint $table) {
            $table->id();  // Menambahkan kolom id otomatis
            $table->string('nama', 100);  // Nama peserta
            $table->string('phone_number', 15);  // Nomor telepon
            $table->string('instagram', 50)->nullable();  // Instagram (opsional)
            $table->string('email', 100);  // Email
            $table->string('payment', 255)->nullable();  // Path bukti pembayaran

            // Menambahkan kolom user_id dan class_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Foreign key untuk users
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');  // Foreign key untuk class_rooms

            $table->timestamps();  // Menambahkan kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_class');
    }
}
