<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'name' => 'Malang',
                'address' => 'Malang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Surabaya',
                'address' => 'Surabaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jakarta',
                'address' => 'Jakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
