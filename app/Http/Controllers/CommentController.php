<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Task $task)
    {
        // 1. Validate the input
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // 2. Create the comment linked to the task, user, and tenant
        $task->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // 3. Go back to the dashboard with a success message
        return back()->with('success', 'Comment posted!');
    }
}