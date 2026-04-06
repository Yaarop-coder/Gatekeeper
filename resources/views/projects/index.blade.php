@extends('layouts.app')

@section('content')
<div x-data="{ 
    drawerOpen: false, 
    activeTask: null,
    search: '',
    showCompleted: true,
    viewMode: 'list',
    filterPriority: 'all', 
    filterUrgency: 'all',
    openTask(task) {
        if(task.due_at && task.due_at.includes('T')) {
            task.due_at = task.due_at.split('T')[0];
        }
        this.activeTask = task;
        this.drawerOpen = true;
    }
}" class="relative">

    <div class="min-h-screen bg-[#f8fafc] text-slate-900 font-sans antialiased">
        
        <nav class="bg-white border-b border-slate-200 px-8 py-4 mb-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <span class="bg-indigo-600 text-white p-2 rounded-lg font-black tracking-tighter text-xl shadow-sm">GK</span>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Gatekeeper <span class="text-slate-400 font-medium text-sm ml-2 italic">v3.5</span></h1>
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

        @if ($errors->any())
            <div class="max-w-7xl mx-auto px-6 mb-4">
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-3 animate-bounce">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Wait! {{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-6 grid grid-cols-12 gap-8">
            
            <aside class="col-span-12 lg:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6 text-center">Live Productivity</h3>
                    <div class="space-y-4">
                        @foreach(['todo' => ['bg-slate-300', 'To-Do'], 'active' => ['bg-indigo-500', 'Active'], 'review' => ['bg-amber-400', 'Review'], 'done' => ['bg-emerald-500', 'Completed']] as $key => $meta)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $meta[0] }}"></div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">{{ $meta[1] }}</span>
                            </div>
                            <span class="text-sm font-black text-slate-700">{{ $stats[$key] ?? 0 }}</span>
                        </div>
                        @endforeach
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
                
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full md:w-96">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input x-model="search" type="text" placeholder="Search tasks..." 
                                   class="block w-full pl-10 pr-3 py-2 border border-slate-100 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">List</button>
                                <button @click="viewMode = 'board'" :class="viewMode === 'board' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">Board</button>
                            </div>
                            <button @click="showCompleted = !showCompleted" :class="showCompleted ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-slate-50 text-slate-400 border-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                                <span x-text="showCompleted ? 'Showing All' : 'Active Only'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-4 border-t border-slate-100 w-full items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2">Quick Filters:</span>
                        <button @click="filterPriority = (filterPriority === 'high' ? 'all' : 'high')" :class="filterPriority === 'high' ? 'bg-red-500 text-white' : 'bg-white text-slate-500 border-slate-200'" class="px-3 py-1 rounded-full text-[10px] font-bold border transition-all">🔥 High Priority</button>
                        <button @click="filterUrgency = (filterUrgency === 'overdue' ? 'all' : 'overdue')" :class="filterUrgency === 'overdue' ? 'bg-amber-500 text-white' : 'bg-white text-slate-500 border-slate-200'" class="px-3 py-1 rounded-full text-[10px] font-bold border transition-all">⏰ Overdue</button>
                        <button @click="filterPriority = 'all'; filterUrgency = 'all'; search = ''" class="ml-auto text-[10px] font-bold text-indigo-600 hover:underline">Reset All</button>
                    </div>
                </div>

                @forelse($projects as $project)
                    <div x-show="viewMode === 'list'">
                        <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                            <header class="px-8 py-6 flex justify-between items-center border-b border-slate-100 bg-slate-50/30">
                                <div class="flex items-center gap-4">
                                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $project->name }}</h2>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete project?')">
                                        @csrf @method('DELETE')
                                        <button class="text-slate-300 hover:text-red-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </form>
                                </div>
                            </header>

                            <div class="divide-y divide-slate-50">
                                @foreach($project->tasks as $task)
                                <div x-show="(search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())) && 
                                             (showCompleted || '{{ $task->status }}' !== 'done') &&
                                             (filterPriority === 'all' || '{{ $task->priority }}' === filterPriority) &&
                                             (filterUrgency === 'all' || (filterUrgency === 'overdue' && {{ $task->due_at && $task->due_at->isPast() ? 'true' : 'false' }}))"
                                     class="group px-8 py-5 hover:bg-slate-50/40 transition-all flex items-center justify-between">
                                    
                                    <div class="flex items-center gap-4 flex-1">
                                        <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <select onchange="this.form.submit()" name="status" class="bg-slate-100 border-none p-1 text-[9px] font-black uppercase rounded cursor-pointer {{ $task->status == 'done' ? 'text-emerald-600 bg-emerald-50' : 'text-indigo-600' }}">
                                                <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>Todo</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>Active</option>
                                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                            </select>
                                        </form>

                                        <button @click="openTask({{ json_encode($task->load(['comments.user', 'assignee'])) }})" 
                                                class="text-sm font-semibold text-slate-700 hover:text-indigo-600 {{ $task->status == 'done' ? 'line-through text-slate-300' : '' }}">
                                            {{ $task->title }}
                                        </button>

                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase border {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-100' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') }}">
                                            {{ $task->priority }}
                                        </span>

                                        @if($task->due_at)
                                            @php
                                                $isOverdue = $task->due_at->isPast() && $task->status !== 'done';
                                                $isSoon = !$isOverdue && ($task->due_at->isToday() || $task->due_at->diffInDays() < 2) && $task->status !== 'done';
                                                $dateClass = $isOverdue ? 'bg-red-50 text-red-500 border-red-100' : ($isSoon ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-400 border-slate-100');
                                            @endphp
                                            <div class="flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold border {{ $dateClass }}">
                                                {{ $task->due_at->format('M d') }}
                                            </div>
                                        @endif

                                        <form action="{{ route('tasks.assign', $task) }}" method="POST" class="ml-auto flex items-center gap-3">
                                            @csrf @method('PATCH')
                                            @if($task->assignee)
                                                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold border-2 border-white shadow-sm ring-1 ring-slate-100 uppercase">{{ $task->assignee->initials }}</div>
                                            @endif
                                            <select name="user_id" onchange="this.form.submit()" class="bg-transparent border-none text-[10px] text-slate-400 focus:ring-0 cursor-pointer hover:text-indigo-600 font-bold uppercase p-0">
                                                <option value="">Assign</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <footer class="bg-slate-50/50 px-8 py-5 border-t border-slate-100">
                                <form action="{{ route('projects.tasks.store', $project) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
                                    @csrf
                                    <input type="text" name="title" placeholder="+ Add task..." required class="flex-1 w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 px-4 outline-none border">
                                    <div class="flex items-center gap-3 w-full md:w-auto">
                                        <input type="date" name="due_at" min="{{ date('Y-m-d') }}" class="bg-white border-slate-200 rounded-xl text-xs text-slate-500 py-2.5 px-3 border outline-none">
                                        <select name="priority" class="bg-white border-slate-200 rounded-xl text-xs text-slate-500 py-2.5 px-3 border outline-none">
                                            <option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option>
                                        </select>
                                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black shadow-lg">Save Task</button>
                                    </div>
                                </form>
                            </footer>
                        </section>
                    </div>

                    <div x-show="viewMode === 'board'" class="space-y-6 mb-16">
                        <h2 class="text-xl font-extrabold text-slate-800 ml-2 tracking-tight">{{ $project->name }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                            @foreach(['todo' => ['To Do', 'bg-slate-400'], 'in_progress' => ['Active', 'bg-indigo-500'], 'review' => ['Review', 'bg-amber-400'], 'done' => ['Completed', 'bg-emerald-500']] as $statusKey => $meta)
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center justify-between px-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full {{ $meta[1] }}"></div>
                                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $meta[0] }}</h4>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $project->tasks->where('status', $statusKey)->count() }}</span>
                                    </div>
                                    <div class="space-y-3 min-h-[200px] p-2 rounded-xl bg-slate-50/50 border border-dashed border-slate-200" 
                                         x-sortable data-status="{{ $statusKey }}">
                                        @foreach($project->tasks->where('status', $statusKey) as $task)
                                            <div x-show="(search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())) &&
                                                         (filterPriority === 'all' || '{{ $task->priority }}' === filterPriority) &&
                                                         (filterUrgency === 'all' || (filterUrgency === 'overdue' && {{ $task->due_at && $task->due_at->isPast() ? 'true' : 'false' }}))"
                                                 @click="openTask({{ json_encode($task->load(['comments.user', 'assignee'])) }})" 
                                                 data-id="{{ $task->id }}"
                                                 class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md cursor-grab active:cursor-grabbing transition-all group">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="px-2 py-0.5 rounded text-[7px] font-black uppercase border {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-100' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') }}">{{ $task->priority }}</span>
                                                    @if($task->assignee)
                                                        <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[8px] font-bold border uppercase">{{ $task->assignee->initials }}</div>
                                                    @endif
                                                </div>
                                                <h5 class="text-xs font-bold text-slate-700 leading-snug group-hover:text-indigo-600 transition-colors mb-2">{{ $task->title }}</h5>
                                                @if($task->due_at)
                                                    @php
                                                        $isOverdue = $task->due_at->isPast() && $task->status !== 'done';
                                                        $textColor = $isOverdue ? 'text-red-600' : 'text-slate-400';
                                                    @endphp
                                                    <div class="mt-4 flex items-center gap-1 text-[9px] font-black uppercase {{ $textColor }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        {{ $task->due_at->format('M d') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white border border-dashed rounded-3xl text-slate-400 italic shadow-sm"><p>No projects found.</p></div>
                @endforelse
            </main>
        </div>
    </div>

    <div x-show="drawerOpen" x-cloak x-transition:opacity @click="drawerOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <aside x-show="drawerOpen" x-cloak
           x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
           class="fixed right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl z-50 overflow-y-auto border-l border-slate-200">
        
        <div x-show="activeTask" class="p-8">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500" x-text="'Task ID #' + activeTask.id"></span>
                    <h2 class="text-2xl font-extrabold text-slate-800 leading-tight mt-1" x-text="activeTask.title"></h2>
                </div>
                <button @click="drawerOpen = false" class="text-slate-400 hover:text-slate-600 text-3xl font-light">&times;</button>
            </div>

            <form :action="`/tasks/${activeTask.id}`" method="POST" class="space-y-6">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 ml-1">Notes & Description</label>
                    <textarea name="description" x-model="activeTask.description" rows="5" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 border" placeholder="Write details..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 ml-1">Priority</label>
                        <select name="priority" x-model="activeTask.priority" class="w-full bg-slate-50 border rounded-xl p-3 text-sm outline-none">
                            <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 ml-1">Due Date</label>
                        <input type="date" name="due_at" x-model="activeTask.due_at" class="w-full bg-slate-50 border rounded-xl p-3 text-sm outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold hover:bg-indigo-600 transition shadow-lg">Update Task</button>
            </form>

            <div class="mt-12 pt-10 border-t border-slate-100">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Discussion</h3>
                <form :action="activeTask ? `/tasks/${activeTask.id}/comments` : '#'" method="POST" class="mb-8">
                    @csrf
                    <div class="relative">
                        <textarea name="body" rows="2" required class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 pr-12 text-sm outline-none focus:bg-white border transition-all" placeholder="Add a comment..."></textarea>
                        <button type="submit" class="absolute bottom-3 right-3 text-indigo-600 hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </button>
                    </div>
                </form>

                <div class="space-y-6 pb-10">
                    <template x-for="comment in activeTask.comments" :key="comment.id">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold border uppercase" x-text="comment.user ? comment.user.name.split(' ').map(n => n[0]).join('').substring(0,2) : '??'"></div>
                            <div class="flex-1 min-w-0">
                                <div class="bg-slate-50 rounded-2xl p-3 px-4 inline-block max-w-full">
                                    <p class="text-[10px] font-black text-slate-800 mb-1" x-text="comment.user ? comment.user.name : 'User'"></p>
                                    <p class="text-xs text-slate-600 leading-relaxed break-words" x-text="comment.body"></p>
                                </div>
                                <span class="text-[9px] text-slate-400 font-medium ml-2 mt-1 block" x-text="new Date(comment.created_at).toLocaleDateString()"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </aside>
</div> 

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('sortable', (el) => {
            new Sortable(el, {
                group: 'tasks', 
                animation: 150,
                ghostClass: 'bg-indigo-50',
                onEnd: (evt) => {
                    const taskId = evt.item.getAttribute('data-id');
                    const newStatus = evt.to.getAttribute('data-status');
                    fetch(`/tasks/${taskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection