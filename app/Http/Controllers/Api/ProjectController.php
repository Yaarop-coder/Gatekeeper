<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
{
    $user = auth()->user();
    $tenantId = $user->tenant_id;

    $users = User::where('tenant_id', $tenantId)->get();

    // Fetch projects with tasks pre-loaded
    $projects = Project::where('tenant_id', $tenantId)
        ->with('tasks') 
        ->get();

    // OPTIMIZED STATS: Use the already fetched projects to count statuses
    // This saves 4 database queries!
    $allTasks = $projects->pluck('tasks')->flatten();
    
    $stats = [
        'todo'   => $allTasks->where('status', 'todo')->count(),
        'active' => $allTasks->where('status', 'in_progress')->count(),
        'review' => $allTasks->where('status', 'review')->count(),
        'done'   => $allTasks->where('status', 'done')->count(),
    ];

    $activities = Activity::whereHas('user', function ($q) use ($tenantId) {
        $q->where('tenant_id', $tenantId);
    })->with('user')->latest()->take(10)->get();

    // FETCH THE API DATA
    try {
        // We add a timeout so if the API is slow, your dashboard doesn't hang forever
        $response = Http::timeout(3)->get('https://zenquotes.io/api/random');
        $quoteData = $response->json();
        $quote = $quoteData[0]['q'] ?? 'Stay focused and keep building.';
        $author = $quoteData[0]['a'] ?? 'Gemini';
    } catch (\Exception $e) {
        $quote = 'Focus on the journey, not the destination.';
        $author = 'Proverb';
    }

    return view('projects.index', compact('stats', 'activities', 'users', 'projects', 'quote', 'author'));
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
