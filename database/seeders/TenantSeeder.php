<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Create Tenant 1
    $tenant1 = \App\Models\Tenant::create(['name' => 'Apple', 'slug' => 'apple']);
    \App\Models\User::create([
        'name' => 'Steve Jobs',
        'email' => 'steve@apple.com',
        'password' => bcrypt('password'),
        'tenant_id' => $tenant1->id,
    ]);

    // Create Tenant 2
    $tenant2 = \App\Models\Tenant::create(['name' => 'Microsoft', 'slug' => 'microsoft']);
    \App\Models\User::create([
        'name' => 'Bill Gates',
        'email' => 'bill@microsoft.com',
        'password' => bcrypt('password'),
        'tenant_id' => $tenant2->id,
    ]);
    // Create the Tenant
    $tenant = \App\Models\Tenant::create([
        'id' => 4,
        'name' => 'Google',
        'slug' => 'google'
    ]);

    // Create the User linked to that Tenant
    \App\Models\User::create([
        'name' => 'Sundar',
        'email' => 'sundar@google.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'tenant_id' => $tenant->id
    ]);
}
}
