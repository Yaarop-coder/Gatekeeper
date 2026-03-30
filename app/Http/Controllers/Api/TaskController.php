<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Store a new task.
     * The TaskObserver will automatically log "created task: {title}"
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'due_at' => 'nullable|date',
            'assigned_to_id' => 'nullable|exists:users,id',
        ]);

        // Note: tenant_id is handled by your BelongsToTenant trait automatically!
        $project->tasks()->create([
            'title' => $request->title,
            'priority' => $request->priority,
            'due_at' => $request->due_at,
            'assigned_to_id' => $request->assigned_to_id,
        ]);

        return back()->with('success', 'Task added successfully!');
    }

    /**
     * Toggle the completion status.
     * The TaskObserver will automatically log "completed/reopened task: {title}"
     */
    public function toggle(Task $task)
    {
        // Simple flip of the boolean
        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        return back()->with('success', 'Task status updated!');
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
