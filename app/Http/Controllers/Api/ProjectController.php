<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use AuthorizesRequests;
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

   $quoteData = Cache::remember('daily_quote', now()->addDay(), function () {
        try {
            $response = Http::timeout(3)->get('https://zenquotes.io/api/random');
            return $response->json()[0] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    });

    $quote = $quoteData['q'] ?? 'Keep building, keep learning.';
    $author = $quoteData['a'] ?? 'Gemini';

    return view('projects.index', compact('stats', 'activities', 'users', 'projects', 'quote', 'author'));
}

    /**
     * Save a new project.
     */
    public function store(Request $request)
{
    // 1. Validate (Crucial!)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB
    ]);

    // 2. Default path to null
    $path = null;

    // 3. Handle the file upload
    if ($request->hasFile('cover_image')) {
        // Use the validated data and store it
        $path = $request->file('cover_image')->store('projects/' . auth()->user()->tenant_id, 'public');
    }

    // 4. Create the Project (Make sure cover_image is being populated)
    Project::create([
        'name' => $validated['name'],
        'cover_image' => $path, // <--- MAKE SURE THIS IS SET
        'tenant_id' => auth()->user()->tenant_id,
    ]);

    return back()->with('success', 'Project created with cover!');
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
    // This automatically checks the ProjectPolicy 'delete' method
    $this->authorize('delete', $project);

    $project->delete();

    return redirect()->route('projects.index')->with('success', 'Project deleted.');
}
public function exportPDF(Project $project)
{
    // Load the project with all its tasks
    $project->load('tasks');

    // Create the PDF from a blade view
    $pdf = Pdf::loadView('pdf.project-report', compact('project'));

    // Download the file with a clean name
    return $pdf->download("Project_Report_{$project->name}.pdf");
}
}
