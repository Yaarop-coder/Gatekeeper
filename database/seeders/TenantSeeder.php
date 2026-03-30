<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Tenant 1
        $tenant1 = Tenant::create(['name' => 'Apple', 'slug' => 'apple']);
        User::create([
            'name' => 'Steve Jobs',
            'email' => 'steve@apple.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenant1->id,
        ]);

        // Create Tenant 2
        $tenant2 = Tenant::create(['name' => 'Microsoft', 'slug' => 'microsoft']);
        User::create([
            'name' => 'Bill Gates',
            'email' => 'bill@microsoft.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenant2->id,
        ]);
        // Create the Tenant
        $tenant = Tenant::create([
            'id' => 4,
            'name' => 'Google',
            'slug' => 'google',
        ]);

        // Create the User linked to that Tenant
        User::create([
            'name' => 'Sundar',
            'email' => 'sundar@google.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
        ]);
    }
}
