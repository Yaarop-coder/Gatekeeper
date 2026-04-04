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
        $request->validate(['body' => 'required|string|max:1000']);

        $task->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return back()->with('success', 'Note added!');
    }
}
