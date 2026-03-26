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

        $project = Project::create([
            'name' => $request->name,
            'tenant_id' => $request->header('X-Tenant-ID'),
        ]);

        return response()->json($project, 201);
    }
}
