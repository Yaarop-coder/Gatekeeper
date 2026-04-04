<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;

class TaskObserver
{
    public function created(Task $task): void
    {
        // 1. Log Activity
        Activity::create([
            'tenant_id' => $task->tenant_id ?? auth()->user()->tenant_id,
            'user_id' => auth()->id() ?? $task->project->user_id, // Fallback to project owner
            'description' => 'created task: '.$task->title,
        ]);

        // 2. Notify (Fixed column name from assigned_to_id to assigned_to)
        if ($task->assigned_to) {
            $user = User::find($task->assigned_to);
            if ($user) {
                $user->notify(new TaskAssigned($task));
            }
        }
    }

    public function updated(Task $task): void
    {
        // Log Completion/Reopen
        if ($task->wasChanged('is_completed')) {
            $status = $task->is_completed ? 'completed' : 'reopened';
            $this->log($task, "{$status} task: {$task->title}");
        }

        // NEW: Log Status changes (e.g., Todo -> Active)
        if ($task->wasChanged('status')) {
            $newStatus = strtoupper(str_replace('_', ' ', $task->status));
            $this->log($task, "moved '{$task->title}' to {$newStatus}");
        }

        // NEW: Log changes in Assignment
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            $user = User::find($task->assigned_to);
            $this->log($task, "assigned '{$task->title}' to ".($user->name ?? 'someone'));
        }
    }

    // Private helper to keep code clean
    private function log(Task $task, $message)
    {
        Activity::create([
            'tenant_id' => $task->tenant_id,
            'user_id' => auth()->id() ?? $task->project->user_id,
            'description' => $message,
        ]);
    }
}
