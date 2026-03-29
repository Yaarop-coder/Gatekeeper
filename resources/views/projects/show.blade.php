@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-6 inline-flex items-center gap-2 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Projects
    </a>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 shadow-sm rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ $project->name }}</h2>
                <p class="text-gray-500 mt-1">Project ID: #{{ $project->id }} • Tenant: {{ auth()->user()->tenant->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $project->tasks->count() }} Total Tasks
                </span>
            </div>
        </div>
    </div>

    <div class="mb-10 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Quick Add Task</h3>
        <form action="{{ route('tasks.store', $project->id) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="title" 
                   class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border text-gray-700" 
                   placeholder="E.g. Finalize module 2 on Coursera..." required>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md active:scale-95">
                Add Task
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl border border-gray-100 rounded-2xl overflow-hidden">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Project Checklist</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse($project->tasks as $task)
                <li class="px-6 py-5 flex items-center justify-between hover:bg-gray-50 transition group">
                    <div class="flex items-center gap-4">
                        <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="flex items-center">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" 
                                   onChange="this.form.submit()" 
                                   {{ $task->is_completed ? 'checked' : '' }}
                                   class="h-6 w-6 text-indigo-600 border-gray-300 rounded-lg focus:ring-indigo-500 cursor-pointer transition">
                        </form>
                        
                        <span class="text-lg {{ $task->is_completed ? 'line-through text-gray-400 font-normal' : 'text-gray-800 font-semibold' }}">
                            {{ $task->title }}
                        </span>
                    </div>

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 p-2 text-gray-300 hover:text-red-500 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </li>
            @empty
                <li class="px-6 py-12 text-center">
                    <div class="text-gray-300 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">No tasks found for this project.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection