<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
{
    $request->validate(['title' => 'required|string|max:255']);

    // Notice: NO tenant_id here. 
    // The Trait sees the Task is being created and "whispers" the tenant_id into the database for you.
    $project->tasks()->create([
        'title' => $request->title,
    ]);

    return back()->with('success', 'Task added!');
}
public function toggle(Task $task)
{
    $task->update([
        'is_completed' => !$task->is_completed
    ]);

    return back();
}
public function destroy(Task $task)
{
    $task->delete();
    return back()->with('success', 'Task removed.');
}
}