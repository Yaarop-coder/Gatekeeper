<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
    // 1. Fetch the projects (The Global Scope still filters these!)
        $projects = \App\Models\Project::all();

    // 2. Return the Blade view instead of JSON
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
}
