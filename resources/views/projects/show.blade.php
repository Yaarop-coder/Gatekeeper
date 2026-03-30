@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold mb-6 inline-flex items-center gap-2">
        ← Back to Dashboard
    </a>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-r-lg font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-4xl font-black text-gray-900">{{ $project->name }}</h2>
            <p class="text-gray-400">Tenant: {{ auth()->user()->tenant->name }}</p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-lg text-indigo-700 font-black text-sm">
            {{ $project->tasks->where('is_completed', true)->count() }} / {{ $project->tasks->count() }} TASKS DONE
        </div>
    </div>

    <div class="mb-10 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <form action="{{ route('tasks.store', $project->id) }}" method="POST" class="flex flex-col lg:flex-row gap-4">
            @csrf
            <input type="text" name="title" required placeholder="Task title..." 
                   class="flex-1 rounded-xl border-gray-300 p-3 border">

            <select name="priority" required class="rounded-xl border-gray-300 p-3 border bg-white font-bold text-gray-600">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>

            <select name="assigned_to_id" class="rounded-xl border-gray-300 p-3 border bg-white font-bold text-gray-600">
                <option value="">Unassigned</option>
                @foreach($team as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>

            <input type="date" name="due_at" class="rounded-xl border-gray-300 p-3 border">

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-8 rounded-xl transition shadow-lg">
                ADD
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl border border-gray-100 rounded-2xl overflow-hidden">
        <ul class="divide-y divide-gray-100">
            @forelse($project->tasks as $task)
                <li class="px-6 py-5 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="checkbox" onChange="this.form.submit()" {{ $task->is_completed ? 'checked' : '' }}
                                   class="h-6 w-6 text-indigo-600 border-gray-300 rounded">
                        </form>
                        <div class="flex flex-col">
                            <span class="{{ $task->is_completed ? 'line-through text-gray-400' : 'text-gray-800 font-bold text-lg' }}">
                                {{ $task->title }}
                            </span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $task->priority }}
                                </span>
                                
                                @if($task->assignedUser)
                                    <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-bold">
                                        @ {{ $task->assignedUser->name }}
                                    </span>
                                @endif

                                @if($task->due_at)
                                    <span class="text-[11px] {{ $task->due_at->isPast() && !$task->is_completed ? 'text-red-500 font-bold' : 'text-gray-400' }}">
                                        Due: {{ $task->due_at->format('M d') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-10 text-center text-gray-400 italic">No tasks yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection