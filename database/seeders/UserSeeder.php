<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Anton',
            'email' => 'kadin@example.com',
            'password' => Hash::make('kadin'),
            'role' => 'Kepala Dinas',
        ]);

        User::create([
            'name' => 'Budi',
            'email' => 'kabag1@example.com',
            'password' => Hash::make('kabag1'),
            'role' => 'Kepala Bagian 1',
        ]);

        User::create([
            'name' => 'Cahyo',
            'email' => 'kabag2@example.com',
            'password' => Hash::make('kabag2'),
            'role' => 'Kepala Bagian 2',
        ]);

        User::create([
            'name' => 'Dedi',
            'email' => 'staf1@example.com',
            'password' => Hash::make('staf1'),
            'role' => 'Staf Bagian 1',
        ]);

        User::create([
            'name' => 'Eka',
            'email' => 'staf2@example.com',
            'password' => Hash::make('staf2'),
            'role' => 'Staf Bagian 2',
        ]);
    }
}
