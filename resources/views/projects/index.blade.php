@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Project Dashboard</h1>
            <p class="text-gray-500 font-medium">Manage your team's progress and attachments.</p>
        </div>

        <div class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm w-full md:w-auto">
            <form action="{{ route('projects.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="New Project Name..." required 
                    class="border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full md:w-64">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-black transition">
                    Create
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4 space-y-8">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">My Tasks</p>
                    <p class="text-3xl font-black text-indigo-600 mt-1">{{ $stats['my_pending_tasks'] ?? 0 }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Projects</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['total_projects'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-gray-900 rounded-3xl p-6 shadow-xl shadow-gray-200">
                <h2 class="text-white font-black text-sm uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Activity
                </h2>
                <div class="space-y-6">
                    @forelse($activities as $activity)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-400">
                                {{ substr($activity->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-300"><span class="font-bold text-white">{{ $activity->user->name ?? 'User' }}</span> {{ $activity->description }}</p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-xs italic">No recent activity.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            @forelse($projects as $project)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6 group/project">
                    
                    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center bg-gradient-to-r from-white to-gray-50/50 gap-4">
                        <div class="flex items-center gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $project->name }}</h2>
                                <p class="text-xs text-gray-400 font-bold uppercase mt-1">
                                    {{ $project->tasks_count }} Tasks • {{ $project->tasks_done_count }} Done
                                </p>
                            </div>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project and all its tasks?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-200 hover:text-red-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                        
                        <div class="w-full md:w-48">
                            @php
                                $total = (int) $project->tasks_count;
                                $done = (int) $project->tasks_done_count;
                                $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                            @endphp
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-black uppercase text-gray-400">Progress</span>
                                <span class="text-[10px] font-black text-indigo-600">{{ $percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-full rounded-full transition-all duration-700" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach($project->tasks as $task)
                            <div class="border border-gray-50 rounded-2xl p-4 bg-white shadow-sm group/task relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-5 h-5 rounded border-2 {{ $task->is_completed ? 'bg-green-500 border-green-500' : 'border-gray-200' }} flex items-center justify-center">
                                                @if($task->is_completed) <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> @endif
                                            </button>
                                        </form>
                                        
                                        <span class="font-bold text-gray-800 {{ $task->is_completed ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</span>
                                        
                                        @if($task->attachment_path)
                                            <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-indigo-400 hover:text-indigo-600 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>

                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-400 transition-opacity opacity-0 group-hover/task:opacity-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="ml-8 space-y-2 border-l-2 border-gray-50 pl-4">
                                    @foreach($task->comments as $comment)
                                        <div class="flex items-start gap-2">
                                            <div class="w-5 h-5 bg-indigo-50 rounded-full flex items-center justify-center text-[8px] font-black text-indigo-500 uppercase">{{ substr($comment->user->name, 0, 1) }}</div>
                                            <div class="bg-gray-50 p-2 rounded-xl flex-1">
                                                <p class="text-[10px] text-gray-600 leading-snug">{{ $comment->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <form action="{{ route('comments.store', $task) }}" method="POST" class="flex gap-2 pt-2">
                                        @csrf
                                        <input type="text" name="body" placeholder="Add a note..." required class="flex-1 border-none bg-transparent text-[10px] focus:ring-0 p-0 placeholder-gray-300">
                                        <button type="submit" class="text-indigo-400 hover:text-indigo-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-6 bg-gray-50/50 border-t border-gray-50">
                        <form action="{{ route('projects.tasks.store', $project) }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3">
                            @csrf
                            <input type="text" name="title" placeholder="What needs to be done?" required class="flex-1 border-gray-200 rounded-xl text-sm focus:ring-indigo-500">
                            <div class="flex gap-2">
                                <select name="priority" class="border-gray-200 rounded-xl text-sm bg-white px-3">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <label class="cursor-pointer bg-white border border-gray-200 rounded-xl px-3 flex items-center hover:bg-gray-50 transition">
                                    <input type="file" name="attachment" class="hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                </label>
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                                    Add Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No Projects Found</p>
                    <p class="text-gray-500 text-sm mt-1">Start by creating a new project above.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection