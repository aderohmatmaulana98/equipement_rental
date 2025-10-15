<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jangan truncate, gunakan delete atau updateOrInsert
        // DB::table('roles')->truncate(); // Hapus ini

        // Data roles
        $roles = [
            [
                'id' => 1,
                'name' => 'Pemilik',
                'slug' => 'pemilik',
                'description' => 'Full access to all system features',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access with limited permissions',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with basic permissions',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Gunakan updateOrInsert untuk setiap role
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']], // kondisi pencarian
                $role // data yang akan diupdate atau insert
            );
        }
    }
}