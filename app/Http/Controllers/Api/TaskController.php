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
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'due_at' => 'nullable|date',
            'assigned_to_id' => 'nullable|exists:users,id', // Make sure the user actually exists
        ]);

        $project->tasks()->create([
            'title' => $request->title,
            'priority' => $request->priority,
            'due_at' => $request->due_at,
            'assigned_to_id' => $request->assigned_to_id, // Save the assignee!
        ]);

        return back()->with('success', 'Task assigned and created!');
    }

    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
