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
        // 1. Log the Activity (The real code, no more [...])
        Activity::create([
            'tenant_id' => $task->tenant_id ?? auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'description' => 'created task: '.$task->title,
        ]);

        // 2. Notify the assigned user
        if ($task->assigned_to_id) {
            $user = User::find($task->assigned_to_id);
            if ($user) {
                $user->notify(new TaskAssigned($task));
            }
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('is_completed')) {
            $status = $task->is_completed ? 'completed' : 'reopened';

            Activity::create([
                'tenant_id' => $task->tenant_id,
                'user_id' => auth()->id(),
                'description' => "{$status} task: ".$task->title,
            ]);
        }
    }
}
