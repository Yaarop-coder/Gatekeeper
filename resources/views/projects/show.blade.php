@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold mb-6 inline-flex items-center gap-2 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Dashboard
    </a>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 shadow-sm rounded-r-lg">
            <ul class="list-disc pl-5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 shadow-sm rounded-r-lg font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ $project->name }}</h2>
                <p class="text-gray-400 mt-1 font-medium italic">Project #{{ $project->id }} — {{ auth()->user()->tenant->name }}</p>
            </div>
            <div class="bg-indigo-50 px-6 py-3 rounded-xl border border-indigo-100">
                <span class="text-indigo-700 text-sm font-black uppercase tracking-widest">
                    {{ $project->tasks->where('is_completed', true)->count() }} / {{ $project->tasks->count() }} Done
                </span>
            </div>
        </div>
    </div>

    <div class="mb-10 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Quick Add Task</h3>
        <form action="{{ route('tasks.store', $project->id) }}" method="POST" class="flex flex-col lg:flex-row gap-4">
            @csrf
            <input type="text" name="title" required placeholder="What needs to be done?" 
                   class="flex-1 rounded-xl border-gray-300 focus:ring-indigo-500 p-3 border shadow-sm text-gray-700">

            <select name="priority" required 
                    class="rounded-xl border-gray-300 focus:ring-indigo-500 p-3 border bg-white shadow-sm font-bold text-gray-600 min-w-[160px]">
                <option value="low">Low Priority</option>
                <option value="medium" selected>Medium Priority</option>
                <option value="high">High Priority</option>
            </select>

            <input type="date" name="due_at" 
                   class="rounded-xl border-gray-300 focus:ring-indigo-500 p-3 border shadow-sm text-gray-600 font-medium">

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded-xl transition-all shadow-lg active:scale-95">
                ADD
            </button>
        </form>
    </div>

    <div class="bg-white shadow-2xl border border-gray-100 rounded-2xl overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Task Management</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse($project->tasks as $task)
                <li class="px-6 py-5 flex items-center justify-between hover:bg-indigo-50/30 transition group">
                    <div class="flex items-center gap-5">
                        <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" 
                                   onChange="this.form.submit()" 
                                   {{ $task->is_completed ? 'checked' : '' }}
                                   class="h-7 w-7 text-indigo-600 border-gray-300 rounded-lg focus:ring-indigo-500 cursor-pointer transition shadow-sm">
                        </form>
                        
                        <div class="flex flex-col">
                            <span class="text-lg {{ $task->is_completed ? 'line-through text-gray-300 font-normal' : 'text-gray-800 font-bold' }}">
                                {{ $task->title }}
                            </span>
                            
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md tracking-tighter {{ 
                                    $task->priority === 'high' ? 'bg-red-100 text-red-600 border border-red-200' : 
                                    ($task->priority === 'medium' ? 'bg-amber-100 text-amber-600 border border-amber-200' : 'bg-emerald-100 text-emerald-600 border border-emerald-200') 
                                }}">
                                    {{ $task->priority }}
                                </span>

                                @if($task->due_at)
                                    <span class="text-[11px] font-medium flex items-center gap-1 {{ $task->due_at->isPast() && !$task->is_completed ? 'text-red-500 animate-pulse' : 'text-gray-400' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $task->due_at->format('M d') }}
                                        @if($task->due_at->isPast() && !$task->is_completed) <span class="font-black underline ml-1">OVERDUE</span> @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 p-2 text-gray-300 hover:text-red-500 transition-all transform hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </li>
            @empty
                <li class="px-6 py-16 text-center">
                    <div class="text-gray-200 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <p class="text-gray-400 font-bold text-lg italic">No tasks assigned yet. Start by adding one above!</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection