<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Create a new task for a project.
     */
    public function store(Request $request, Project $project)
    {
        // 1. Security Check
        if ($project->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // 2. Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_at' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        // 3. Create Task via Relationship
        $project->tasks()->create([
            'tenant_id' => $project->tenant_id,
            'title' => $validated['title'],
            'due_at' => $validated['due_at'],
            'priority' => $validated['priority'],
            'status' => 'todo',
            'is_completed' => false,
        ]);

        return back()->with('success', 'Task added to '.$project->name);
    }

    /**
     * Handle the Drawer Update (Title, Description, etc.)
     */
    public function update(Request $request, Task $task)
    {
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|string',
            'priority' => 'sometimes|string',
            'due_at' => 'nullable|date',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully!');
    }

    /**
     * Update Task Status (Todo/Active/Review/Done)
     */
    public function updateStatus(Request $request, Task $task)
    {
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $task->update([
            'status' => $request->status,
            'is_completed' => ($request->status === 'done'),
        ]);

        return back()->with('success', 'Status updated.');
    }

    /**
     * Quick toggle for completion checkbox
     */
    public function toggle(Task $task)
    {
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $task->update([
            'is_completed' => ! $task->is_completed,
            'status' => ! $task->is_completed ? 'done' : 'todo',
        ]);

        return back();
    }

    /**
     * Assign a user to the task
     */
    public function assign(Request $request, Task $task)
    {
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $task->update(['assigned_to' => $request->user_id]);

        return back()->with('success', 'Assignee updated!');
    }

    /**
     * Delete the task
     */
    public function destroy(Task $task)
    {
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }
}
