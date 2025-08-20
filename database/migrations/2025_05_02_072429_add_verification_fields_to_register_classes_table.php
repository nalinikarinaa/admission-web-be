<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToRegisterClassesTable extends Migration
{
    public function up()
    {
        Schema::table('register_class', function (Blueprint $table) {
            $table->enum('payment_verification', ['pending', 'lunas', 'gagal'])->default('pending')->after('payment');
            $table->enum('class_verification', ['aktif', 'tidak aktif'])->default('tidak aktif')->after('payment_verification');
        });
    }
    
    public function down()
    {
        Schema::table('register_class', function (Blueprint $table) {
            $table->dropColumn(['payment_verification', 'class_verification']);
        });
    }
    
}
