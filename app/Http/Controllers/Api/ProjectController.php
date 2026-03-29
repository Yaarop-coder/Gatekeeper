<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
{
    // withCount('tasks') adds a 'tasks_count' variable to every project
    // withCount(['tasks as completed_tasks_count' => ...]) counts only finished ones
    $projects = \App\Models\Project::withCount([
        'tasks',
        'tasks as completed_tasks_count' => function ($query) {
            $query->where('is_completed', true);
        }
    ])->get();

    return view('projects.index', compact('projects'));
}

    // 2. Save a new project for the current tenant
    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // We do NOT manually set tenant_id here. 
    // The 'BelongsToTenant' trait handles it automatically!
    \App\Models\Project::create([
        'name' => $request->name,
    ]);

    return redirect()->route('projects.index')->with('success', 'Project created!');
    }
    public function show(\App\Models\Project $project)
    {
    // Load the tasks associated with this project
        $project->load('tasks');

        return view('projects.show', compact('project'));
    }
    public function destroy(\App\Models\Project $project)
    {
    // The 'BelongsToTenant' trait automatically protects this!
    // If Tim (Apple) tries to delete Bill's (Microsoft) project ID, 
    // Laravel won't even find the record. 404 Error!
    
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
