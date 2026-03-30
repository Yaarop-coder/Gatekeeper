<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Activity::create([
            'tenant_id' => $task->tenant_id ?? auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'description' => 'created task: '.$task->title,
        ]);
    }

    public function updated(Task $task): void
    {
        // Only log if the 'is_completed' status actually changed
        if ($task->wasChanged('is_completed')) {
            $status = $task->is_completed ? 'completed' : 'reopened';

            Activity::create([
                'tenant_id' => $task->tenant_id,
                'user_id' => auth()->id(),
                'description' => "{$status} task: ".$task->title,
            ]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
