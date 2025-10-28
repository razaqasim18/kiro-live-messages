<?php

namespace Database\Seeders;

use App\Models\Gift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Gift::insert([
            [
                "name" => "simple",
                "link" => "http://127.0.0.1:8000/storage/gifts/cheers.json",
                "coins" => 10,
            ],
            [
                "name" => "simple Gift",
                "link" => "http://127.0.0.1:8000/storage/gifts/ferrari.json",
                "coins" => 20,
            ],
        ]);
    }
}
