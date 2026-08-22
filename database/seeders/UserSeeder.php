<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@bpkdokumen.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Tim BPK',
            'email' => 'bpk@bpkdokumen.id',
            'password' => Hash::make('bpk123'),
            'role' => 'tim_bpk',
            'is_active' => true,
        ]);
    }
}
