<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Zortrox',
            'email' => 'zortrox@foxslash.test',
            'password' => Hash::make('asdf1234'),
        ]);

        $user->assignRole('Super Admin');
    }
}
