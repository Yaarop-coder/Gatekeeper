<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required',
            'assigned_to_id' => 'nullable|exists:users,id',
        ]);

        $task = $project->tasks()->create([
            'title' => $request->title,
            'priority' => $request->priority,
            'due_at' => $request->due_at,
            'assigned_to_id' => $request->assigned_to_id,
            'tenant_id' => auth()->user()->tenant_id, // Ensure tenant_id is set
        ]);

        // LOG THE ACTIVITY
        Activity::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'description' => 'Created task: '.$task->title,
        ]);

        return back()->with('success', 'Task added and logged!');
    }

    public function toggle(Task $task)
    {
        // 1. Flip the status
        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        // 2. Determine the message based on the new state
        $status = $task->is_completed ? 'completed' : 'reopened';

        // 3. LOG THE ACTIVITY
        Activity::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'description' => "{$status} task: ".$task->title,
        ]);

        return back()->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
