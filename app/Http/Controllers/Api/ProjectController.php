<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
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
        // 1. Security Check: Compare the tenant IDs
        // Make sure you use '->user()' (property) not '->user()()' (method)
        if ($project->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'You do not have permission to view this project.');
        }

        // 2. Load the tasks relationship so they appear on the page
        $project->load('tasks');

        // 3. Return the view
        return view('projects.show', compact('project'));
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
