<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Create the Tenant with a slug
        $tenant = Tenant::create([
            'id' => 1,
            'name' => 'Apple Inc.',
            'slug' => 'apple-inc', 
        ]);

        // 2. Create the Owner User
        $owner = User::create([
            'name' => 'Steve Jobs',
            'email' => 'steve@apple.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'tenant_id' => $tenant->id,
    ]);

    auth()->login($owner);

        // 3. Create a Demo Project
        $project = Project::create([
            'name' => 'Apple Watch Series 10',
            'tenant_id' => $tenant->id,
            'cover_image' => null,
        ]);

        // 4. Create tasks with realistic statuses
        $tasks = [
            ['title' => 'Design OLED Display', 'status' => 'done', 'priority' => 'high'],
            ['title' => 'S10 Chip Integration', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'Battery Life Optimization', 'status' => 'todo', 'priority' => 'medium'],
            ['title' => 'Finalize Band Colors', 'status' => 'todo', 'priority' => 'low'],
        ];

        foreach ($tasks as $taskData) {
    Task::withoutEvents(function () use ($taskData, $project, $tenant, $owner) {
        Task::create([
            'title'          => $taskData['title'],
            'status'         => $taskData['status'],
            'priority'       => $taskData['priority'],
            'project_id'     => $project->id,
            'tenant_id'      => $tenant->id,
            'assigned_to_id' => $owner->id,
            'description'    => 'Demo task'
        ]);
    });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}