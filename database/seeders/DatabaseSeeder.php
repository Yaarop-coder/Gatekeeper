<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only call your custom tenant seeder
        $this->call([
            TenantSeeder::class,
        ]);
    }
}
