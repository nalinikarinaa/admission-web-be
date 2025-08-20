<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToClassRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->string('address')->nullable()->after('location_id'); // atau sesuaikan posisi
        });
    }

    public function down()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
}

