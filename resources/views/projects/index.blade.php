@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4 space-y-8">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">My Tasks</p>
                    <p class="text-3xl font-black text-indigo-600 mt-1">{{ $stats['my_pending_tasks'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Projects</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['total_projects'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="font-black text-gray-900 uppercase tracking-tighter">My Notifications</h2>
                </div>
                <ul class="divide-y divide-gray-50">
                    @forelse($notifications as $notification)
                        <li class="p-4 {{ $notification->read_at ? 'opacity-50' : 'bg-indigo-50/30' }}">
                            <p class="text-xs font-bold text-gray-800">{{ $notification->data['message'] }}</p>
                            <p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">{{ $notification->created_at->diffForHumans() }}</p>
                        </li>
                    @empty
                        <li class="p-6 text-center text-gray-400 text-xs italic">No new alerts.</li>
                    @endforelse
                </ul>
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.read') }}" method="POST" class="p-3 bg-gray-50 text-center">
                        @csrf
                        <button class="text-[10px] font-black text-indigo-600 uppercase hover:underline">Mark as read</button>
                    </form>
                @endif
            </div>

            <div class="bg-gray-900 rounded-3xl p-6 shadow-xl shadow-gray-200">
                <h2 class="text-white font-black text-sm uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Live Activity
                </h2>
                <div class="space-y-6">
                    @foreach($activities as $activity)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-400">
                                {{ substr($activity->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-300"><span class="font-bold text-white">{{ $activity->user->name }}</span> {{ $activity->description }}</p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter mb-8">Active Projects</h1>

            @foreach($projects as $project)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center bg-gradient-to-r from-white to-gray-50/50 gap-4">
    <div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $project->name }}</h2>
        <p class="text-sm text-gray-500 font-medium mt-1">
            {{ $project->tasks_count }} Total Tasks • {{ $project->completed_tasks_count }} Completed
        </p>
    </div>

    <div class="w-full md:w-48">
        @php
    $total = $project->tasks_count;
    $completed = $project->completed_tasks_count;
    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
@endphp
        <div class="flex justify-between items-center mb-2">
            <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Progress</span>
            <span class="text-[10px] font-black text-indigo-600">{{ $percentage }}%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-indigo-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
        </div>
    </div>
</div>

                    <div class="p-6 space-y-3">
                        @foreach($project->tasks as $task)
                            <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-50 hover:border-indigo-100 hover:bg-indigo-50/20 transition group">
                                <div class="flex items-center gap-4">
                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition {{ $task->is_completed ? 'bg-green-500 border-green-500' : 'border-gray-200 bg-white' }}">
                                            @if($task->is_completed)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    
                                    <span class="font-bold {{ $task->is_completed ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                                        {{ $task->title }}
                                    </span>

                                    @if($task->attachment_path)
                                        <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="p-1.5 bg-white border border-gray-100 rounded-lg text-gray-400 hover:text-indigo-600 shadow-sm transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md {{ $task->priority == 'high' ? 'bg-red-50 text-red-500' : ($task->priority == 'medium' ? 'bg-orange-50 text-orange-500' : 'bg-gray-50 text-gray-400') }}">
                                    {{ $task->priority }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-6 bg-gray-50/50 border-t border-gray-50">
                        <form action="{{ route('projects.tasks.store', $project) }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4">
                            @csrf
                            <input type="text" name="title" placeholder="What needs to be done?" required class="flex-1 border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            
                            <select name="priority" class="border-gray-200 rounded-xl text-sm bg-white">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>

                            <div class="relative">
                                <input type="file" name="attachment" id="file-{{ $project->id }}" class="hidden">
                                <label for="file-{{ $project->id }}" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    File
                                </label>
                            </div>

                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                                Add Task
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection