<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ParkingSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // users
        $db->table('users')->insert([
            'email' => 'demo@park.example',
            'password_hash' => null,
            'full_name' => 'Demo User',
            'phone' => '555-0001',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // parking spots
        $db->table('parking_spots')->insertBatch([
            ['label' => 'P1-01', 'location' => 'Level 1', 'is_available' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['label' => 'P1-02', 'location' => 'Level 1', 'is_available' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }
}
