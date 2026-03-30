<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification; // 1. Import the Task model

class TaskAssigned extends Notification
{
    use Queueable;

    public $task; // 2. Declare the public property so it's accessible

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task) // 3. Accept the Task object here
    {
        $this->task = $task; // 4. Assign it to the property
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => 'A new task has been assigned to you: '.$this->task->title,
        ];
    }
}
