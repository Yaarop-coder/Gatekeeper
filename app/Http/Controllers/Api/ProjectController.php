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

        $users = User::where('tenant_id', $user->tenant_id)->get();

        // 1. ADD THIS LINE to get the projects
        $projects = Project::where('tenant_id', $user->tenant_id)
            ->with('tasks') // This allows the component to see the tasks and calculate progress
            ->get();

        $stats = [
            'todo' => Task::where('tenant_id', $user->tenant_id)->where('status', 'todo')->count(),
            'active' => Task::where('tenant_id', $user->tenant_id)->where('status', 'in_progress')->count(),
            'review' => Task::where('tenant_id', $user->tenant_id)->where('status', 'review')->count(),
            'done' => Task::where('tenant_id', $user->tenant_id)->where('status', 'done')->count(),
        ];

        $activities = Activity::whereHas('user', function ($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        })->with('user')->latest()->take(10)->get();

        // 2. ADD 'projects' to the compact() list
        return view('projects.index', compact('stats', 'activities', 'users', 'projects'));
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
