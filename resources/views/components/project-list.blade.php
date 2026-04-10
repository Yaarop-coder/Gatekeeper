<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\User;

new class extends Component
{
    // This is the "Logic" part. It runs before the page loads.
    protected $listeners = ['taskUpdated' => '$refresh'];

    public function with()
    {
        $user = auth()->user();
        return [
            'users' => User::where('tenant_id', $user->tenant_id)->get(),
            'projects' => Project::where('tenant_id', $user->tenant_id)
                ->withCount([
                    'tasks',
                    'tasks as tasks_done_count' => function ($query) {
                        $query->where('status', 'done');
                    },
                ])
                ->with(['tasks' => function ($query) {
                    $query->with(['comments.user', 'assignee'])->latest();
                }])
                ->get(),
        ];
    }
};
?>
{{-- Remove the PHP Volt Logic at the top if it's still there, and use this: --}}

<div x-data="{ 
    search: '', 
    viewMode: 'list', 
    showCompleted: true, 
    filterPriority: 'all', 
    filterUrgency: 'all' 
}" class="space-y-8">

    {{-- Filter Header --}}
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
        @php
            // Calculate progress manually since we are in a standard Blade component
            $total = $project->tasks->count();
            $completed = $project->tasks->where('status', 'done')->count();
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp

        <div x-show="viewMode === 'list'">
            <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <header class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $project->name }}</h2>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete project?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex flex-col items-end gap-1">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Progress</span>
                                    <span class="text-sm font-black text-indigo-600">{{ $percent }}%</span>
                                </div>
                                <div class="w-48 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            <div class="hidden md:flex flex-col items-center px-4 border-l border-slate-200">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tasks</span>
                                <span class="text-sm font-black text-slate-700">{{ $completed }}/{{ $total }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="divide-y divide-slate-50">
                    @foreach($project->tasks as $task)
                         <div x-show="(search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())) && 
                                     (showCompleted || '{{ $task->status }}' !== 'done') &&
                                     (filterPriority === 'all' || '{{ $task->priority }}' === filterPriority)"
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
                                <button @click="openTask({{ json_encode($task) }})" 
                                        class="text-sm font-semibold text-slate-700 hover:text-indigo-600 {{ $task->status == 'done' ? 'line-through text-slate-300' : '' }}">
                                    {{ $task->title }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add Task Inline Form --}}
                <footer class="px-8 py-4 bg-slate-50/50">
                    <form action="{{ route('tasks.store', $project) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="title" placeholder="Add a task to this project..." class="flex-1 bg-white border-slate-200 rounded-xl px-4 py-2 text-sm outline-none border focus:ring-2 focus:ring-indigo-500/10">
                        <button type="submit" class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Add</button>
                    </form>
                </footer>
            </section>
        </div>
        {{-- Board View --}}
        <div x-show="viewMode === 'board'" x-cloak class="flex flex-col gap-12">
    @foreach($projects as $project)
        <div class="space-y-4">
            <h2 class="text-xl font-extrabold text-slate-800 ml-2">{{ $project->name }}</h2>
            
            {{-- This line makes it scrollable on small screens but wide on desktop --}}
            <div class="flex overflow-x-auto pb-6 gap-4 snap-x">
                @foreach(['todo' => 'To Do', 'in_progress' => 'Active', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
                    <div class="min-w-[300px] md:w-1/4 flex-shrink-0 snap-start">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</h3>
                        </div>

                        {{-- The Card Container - Added space-y-4 for vertical gap --}}
                        <div x-sortable 
                             data-status="{{ $status }}" 
                             class="flex flex-col gap-4 p-3 rounded-2xl bg-slate-50/50 border-2 border-dashed border-transparent min-h-[500px]">
                            
                            @foreach($project->tasks->where('status', $status) as $task)
                                <div data-id="{{ $task->id }}" 
                                     @click="openTask({{ json_encode($task) }})"
                                     class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all cursor-grab active:cursor-grabbing flex flex-col min-h-[120px]">
                                    
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed mb-4">{{ $task->title }}</p>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50">
                                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-lg {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $task->priority }}
                                        </span>
                                        @if($task->due_at)
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                                                {{ \Carbon\Carbon::parse($task->due_at)->format('d M') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
    @empty
        <div class="text-center py-20 bg-white border border-dashed rounded-3xl text-slate-400 italic shadow-sm">
            <p>No projects found.</p>
        </div>
    @endforelse
</div>