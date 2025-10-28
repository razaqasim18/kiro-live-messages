<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // User::create([
        //     'name' => 'admin',
        //     'email' => 'admin@admin.com',
        //     'password' => Hash::make('admin'),
        //     'phone' => '09007416765',
        //     'is_admin' => '1',
        //     'gender' => '1',
        //     'coins' => '999',
        // ]);
        // User::factory(10)->create();
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'phone' => '09007416765',
            'is_admin' => '1',
            'gender' => '1',
            'coins' => '999',
        ]);
    }
}
