<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin',
            ]);
        Role::create([
            'name' => 'user',
            ]);
        User::create([
            'email' => 'admin@gmail.com',
            'password' =>hash::make('admin'),
            'role_id' => 1
            ]);
    }
}