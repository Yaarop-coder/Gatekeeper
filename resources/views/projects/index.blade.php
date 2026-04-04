@extends('layouts.app')

@section('content')
<div x-data="{ 
    drawerOpen: false, 
    activeTask: null,
    openTask(task) {
        this.activeTask = task;
        this.drawerOpen = true;
    }
}" class="relative">

    <div class="min-h-screen bg-[#f8fafc] text-slate-900 font-sans antialiased">
        
        <nav class="bg-white border-b border-slate-200 px-8 py-4 mb-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <span class="bg-indigo-600 text-white p-2 rounded-lg font-black tracking-tighter text-xl shadow-sm">GK</span>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Gatekeeper <span class="text-slate-400 font-medium text-sm ml-2 italic">v3.0</span></h1>
                </div>
                
                <form action="{{ route('projects.store') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Start new project..." required class="bg-slate-50 border-slate-200 rounded-full text-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-all w-48 focus:w-64 border outline-none">
                    <button type="submit" class="bg-slate-900 text-white h-9 w-9 rounded-full flex items-center justify-center hover:bg-indigo-600 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </form>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-6 grid grid-cols-12 gap-8">
            
            <aside class="col-span-12 lg:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4 text-center">Workspace Overview</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-slate-50 rounded-xl">
                            <p class="text-2xl font-black text-slate-800">{{ $stats['total_projects'] ?? 0 }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">Projects</p>
                        </div>
                        <div class="text-center p-3 bg-indigo-50 rounded-xl">
                            <p class="text-2xl font-black text-indigo-600">{{ $stats['my_pending_tasks'] ?? 0 }}</p>
                            <p class="text-[9px] text-indigo-400 font-bold uppercase">Active</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-2">Recent Events</h3>
                    @forelse($activities as $activity)
                    <div class="bg-white border border-slate-100 p-3 rounded-xl shadow-sm">
                        <p class="text-[11px] text-slate-600 leading-tight"><strong>{{ $activity->user->name ?? 'User' }}</strong> {{ $activity->description }}</p>
                        <span class="text-[9px] text-slate-400 font-medium italic mt-1 block">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic ml-2">No activity logged.</p>
                    @endforelse
                </div>
            </aside>

            <main class="col-span-12 lg:col-span-9 space-y-8 pb-20">
                @forelse($projects as $project)
                <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden transition-all hover:shadow-md">
                    
                    <header class="px-8 py-6 flex justify-between items-center border-b border-slate-100 bg-slate-50/30">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $project->name }}</h2>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>

                        @php
                            $total = (int) $project->tasks_count;
                            $done = (int) $project->tasks_done_count;
                            $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full border border-slate-100 shadow-sm">
                            <div class="w-20 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-[10px] font-black text-slate-600">{{ $pct }}% Complete</span>
                        </div>
                    </header>

                    <div class="divide-y divide-slate-50">
                        @foreach($project->tasks as $task)
                        <div class="group px-8 py-5 hover:bg-slate-50/40 transition-all flex items-start justify-between">
                            <div class="flex flex-col gap-2 w-full">
                                <div class="flex items-center gap-4">
                                    <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select onchange="this.form.submit()" name="status" class="bg-slate-100 border-none p-1 text-[9px] font-black uppercase rounded cursor-pointer {{ $task->status == 'done' ? 'text-emerald-600 bg-emerald-50' : 'text-indigo-600' }}">
                                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>Todo</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>Active</option>
                                            <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                        </select>
                                    </form>

                                    <button @click="openTask({{ json_encode($task) }})" 
                                            class="text-sm font-semibold text-slate-700 hover:text-indigo-600 transition-colors text-left {{ $task->status == 'done' ? 'line-through text-slate-300' : '' }}">
                                        {{ $task->title }}
                                    </button>

                                    @if($task->due_at)
                                        @php
                                            $isOverdue = $task->due_at->isPast() && $task->status !== 'done';
                                            $isSoon = !$isOverdue && ($task->due_at->isToday() || $task->due_at->diffInDays() < 2) && $task->status !== 'done';
                                        @endphp
                                        <div class="flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border
                                            {{ $isOverdue ? 'bg-red-50 text-red-500 border-red-100' : ($isSoon ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-400 border-slate-100') }}">
                                            {{ $task->due_at->format('M d') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('tasks.assign', $task) }}" method="POST" class="ml-auto md:ml-0">
                                        @csrf @method('PATCH')
                                        <select name="user_id" onchange="this.form.submit()" class="bg-transparent border-none text-[10px] text-slate-400 focus:ring-0 cursor-pointer hover:text-indigo-500 font-medium">
                                            <option value="">Unassigned</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity ml-4">
                                @csrf @method('DELETE')
                                <button class="text-slate-200 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>

                    <footer class="bg-slate-50/50 px-8 py-5 border-t border-slate-100">
                        <form action="{{ route('projects.tasks.store', $project) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
                            @csrf
                            <input type="text" name="title" placeholder="+ Add task..." required class="flex-1 w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 px-4 focus:ring-2 focus:ring-indigo-500/20 border">
                            <div class="flex items-center gap-3 w-full md:w-auto">
                                <input type="date" name="due_at" class="bg-white border-slate-200 rounded-xl text-xs text-slate-500 py-2.5 px-3 border">
                                <select name="priority" required class="bg-white border-slate-200 rounded-xl text-xs text-slate-500 py-2.5 px-3 border outline-none">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black shadow-lg">Save</button>
                            </div>
                        </form>
                    </footer>
                </section>
                @empty
                    <div class="text-center py-20 bg-white border border-dashed rounded-3xl text-slate-400 italic">No projects found. Create one above!</div>
                @endforelse
            </main>
        </div>
    </div>

    <div x-show="drawerOpen" x-cloak x-transition @click="drawerOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>

    <aside x-show="drawerOpen" x-cloak
           x-transition:enter="transition ease-out duration-300 transform"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl z-50 overflow-y-auto border-l border-slate-200">
        
        <div x-show="activeTask" class="p-8">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500" x-text="'Task #' + (activeTask ? activeTask.id : '')"></span>
                    <h2 class="text-2xl font-extrabold text-slate-800 leading-tight mt-1" x-text="activeTask ? activeTask.title : ''"></h2>
                </div>
                <button @click="drawerOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-light">&times;</button>
            </div>

            <form x-show="activeTask" :action="activeTask ? `{{ url('tasks') }}/${activeTask.id}` : '#'" method="POST" class="space-y-6">
                @csrf @method('PATCH')
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Description / Notes</label>
                    <textarea name="description" x-model="activeTask.description" rows="8" 
                              class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500/20 border min-h-[250px] outline-none" 
                              placeholder="Describe this task..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Priority</label>
                        <select name="priority" x-model="activeTask.priority" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Due Date</label>
                        <input type="date" name="due_at" x-model="activeTask.due_at" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm">
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold hover:bg-indigo-600 transition shadow-lg">
                    Update Task Details
                </button>
            </form>
        </div>
    </aside>

</div> @endsection