<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        // 1. Get projects with counts
        $projects = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('is_completed', true);
            },
        ])->get();

        // 2. Calculate Global Stats (Gatekeeper handles the tenant filtering)
        $stats = [
            'total_projects' => $projects->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('is_completed', true)->count(),
        ];

        // 3. PASS BOTH TO THE VIEW
        return view('projects.index', compact('projects', 'stats'));
    }

    // 2. Save a new project for the current tenant
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // We do NOT manually set tenant_id here.
        // The 'BelongsToTenant' trait handles it automatically!
        Project::create([
            'name' => $request->name,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created!');
    }

    public function show(Project $project)
    {
        // 1. Get the user once to avoid multiple calls
        $user = auth()->user();

        // 2. Security: Ensure user exists and matches the project's tenant
        if (! $user || $project->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this project.');
        }

        // 3. Eager load tasks AND their assigned users to avoid "N+1" database issues
        $project->load(['tasks.assignedUser']);

        // 4. Get the team members (all users belonging to the same tenant)
        $team = User::where('tenant_id', $user->tenant_id)->get();

        return view('projects.show', compact('project', 'team'));
    }

    public function destroy(Project $project)
    {
        // The 'BelongsToTenant' trait automatically protects this!
        // If Tim (Apple) tries to delete Bill's (Microsoft) project ID,
        // Laravel won't even find the record. 404 Error!

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
