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
        // Seed Training Provider
        $providerId = \Illuminate\Support\Facades\DB::table('training_providers')->updateOrInsert(
            ['email' => 'naap@gmail.com'],
            [
                'id' => 3,
                'name' => 'NAAP',
                'code' => 'NAAP-01',
                'address' => 'Piccio Garden',
                'phone' => '09495748301',
                'accreditation_course' => 'PPL',
                'atoc_attachment' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

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
        \Illuminate\Support\Facades\DB::table('admins')->updateOrInsert(
            ['email' => 'admin@naap.com'],
            [
                'first_name' => 'Russel',
                'middle_name' => 'S',
                'last_name' => 'Admin',
                'flight_id' => 3,
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
