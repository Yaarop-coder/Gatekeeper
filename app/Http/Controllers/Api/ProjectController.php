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
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Get all team members for the assignment dropdowns
        $users = User::where('tenant_id', $user->tenant_id)->get();

        // 2. Fetch Projects with Progress Counts
        $projects = Project::where('tenant_id', $user->tenant_id)
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => function ($query) {
                    $query->where('status', 'done');
                },
            ])
            ->with(['tasks' => function ($query) {
                // UPDATED: Added 'assignee' to the eager load list
                // This allows $task->assignee->initials to work without extra database hits
                $query->with(['comments.user', 'user', 'assignee'])->latest();
            }])
            ->get();

        // 3. Calculate Stats for the Sidebar
        $stats = [
            'total_projects' => $projects->count(),
            'todo' => Task::where('tenant_id', $user->tenant_id)->where('status', 'todo')->count(),
            'active' => Task::where('tenant_id', $user->tenant_id)->where('status', 'in_progress')->count(),
            'review' => Task::where('tenant_id', $user->tenant_id)->where('status', 'review')->count(),
            'done' => Task::where('tenant_id', $user->tenant_id)->where('status', 'done')->count(),
        ];

        // 4. Fetch the Activity Feed
        $activities = Activity::with('user')
            ->latest()
            ->take(10)
            ->get();

        // 5. Fetch Notifications for the user
        $notifications = $user->notifications()->latest()->take(5)->get();

        return view('projects.index', compact('projects', 'stats', 'activities', 'notifications', 'users'));
    }

    /**
     * Save a new project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Explicitly setting tenant_id ensures no "Field 'tenant_id' doesn't have a default value" errors
        Project::create([
            'name' => $request->name,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created!');
    }

    /**
     * View a single project detail.
     */
    public function show(Project $project)
    {
        $user = auth()->user();

        // Security check
        if ($project->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this project.');
        }

        // Load tasks and the team
        $project->load(['tasks.user', 'tasks.comments.user']);
        $team = User::where('tenant_id', $user->tenant_id)->get();

        return view('projects.show', compact('project', 'team'));
    }

    /**
     * Remove a project.
     */
    public function destroy(Project $project)
    {
        if ($project->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $project->delete();

        return back()->with('success', 'Project deleted!');
    }
}
