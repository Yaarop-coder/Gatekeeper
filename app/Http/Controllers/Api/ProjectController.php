<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // 1. Fetch Projects with Counts and Nested Comments
    $projects = Project::where('tenant_id', $user->tenant_id)
        ->withCount([
            'tasks', 
            'tasks as tasks_done_count' => function ($query) {
                $query->where('is_completed', true);
            }
        ])
        ->with(['tasks' => function($query) {
            $query->with(['comments.user'])->latest();
        }])
        ->get();

    // 2. Define the missing $stats variable
    $stats = [
        'total_projects' => $projects->count(),
        'my_pending_tasks' => \App\Models\Task::where('tenant_id', $user->tenant_id)
            ->where('is_completed', false)
            ->count(),
    ];

    // 3. Define the missing $activities variable (adjust based on your Activity model)
    // If you don't have an Activity model yet, use an empty collection to prevent errors
    $activities = \App\Models\Activity::where('tenant_id', $user->tenant_id)
        ->with('user')
        ->latest()
        ->take(5)
        ->get();

    // 4. Fetch Notifications
    $notifications = $user->notifications()->latest()->take(5)->get();

    // Now compact() will find everything it needs!
    return view('projects.index', compact('projects', 'stats', 'activities', 'notifications'));
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
    // Safety check: ensure the user owns this project's tenant
    if ($project->tenant_id !== auth()->user()->tenant_id) {
        abort(403);
    }

    $project->delete();
    return back()->with('success', 'Project deleted!');
}
}
