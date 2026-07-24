<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed SuperAdmin
        \Illuminate\Support\Facades\DB::table('super_admins')->updateOrInsert(
            ['email' => 'superadmin@naap.com'],
            [
                'first_name' => 'Super',
                'middle_name' => 'A',
                'last_name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed Admin (referencing training provider id 3)
        $provider = \Illuminate\Support\Facades\DB::table('training_providers')->first();
        if ($provider) {
            \Illuminate\Support\Facades\DB::table('admins')->updateOrInsert(
                ['email' => 'admin@naap.com'],
                [
                    'first_name' => 'Russel',
                    'middle_name' => 'S',
                    'last_name' => 'Admin',
                    'flight_id' => $provider->id,
                    'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
