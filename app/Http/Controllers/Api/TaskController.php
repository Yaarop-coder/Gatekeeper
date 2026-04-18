<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        // 1. Security Check
        if ($project->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // 2. Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'due_at' => 'nullable|date',
        ]);

        // 3. Create Task via Relationship
        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'status' => 'todo',
            'due_at' => $validated['due_at'],
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // 4. API INTEGRATION (Discord Notification)
        if ($task->priority === 'high') {
            try {
                Http::post(config('services.discord.webhook_url'), [
                    'content' => "🔥 **New High Priority Task!**\n**Project:** {$project->name}\n**Task:** {$task->title}\n**Due:** ".($task->due_at ?? 'No date set'),
                ]);
            } catch (\Exception $e) {
                // We catch the error so the app doesn't crash if Discord is down.
                // You could log this error if you wanted: \Log::error($e->getMessage());
            }
        }

        return back()->with('success', 'Task created!');
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
        // Security check (Keep this!)
        if ($task->tenant_id !== auth()->user()->tenant_id) {
            return $request->wantsJson()
                ? response()->json(['error' => 'Unauthorized'], 403)
                : abort(403);
        }

        $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $task->update([
            'status' => $request->status,
        ]);

        // SMART RESPONSE:
        // If it's a drag-and-drop (JS), send JSON.
        // If it's a dropdown click (Form), redirect back to the board.
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Task updated successfully']);
        }

        return back()->with('success', 'Status updated!');
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
