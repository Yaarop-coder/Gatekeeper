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
            $total = $project->tasks->count();
            $completed = $project->tasks->where('status', 'done')->count();
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp

        {{-- List View Section --}}
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
        <div class="group flex items-center justify-between p-4 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition-colors">
            
            <div class="flex items-center gap-4">
                {{-- Status and Title --}}
                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-slate-100 text-slate-500">{{ $task->status }}</span>
                <h4 @click="openTask({{ $task->toJson() }})" class="text-sm font-bold text-slate-700 cursor-pointer">{{ $task->title }}</h4>
            </div>

            <div class="flex items-center gap-6">
                {{-- Priority and Date --}}
                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' }}">
                    {{ $task->priority }}
                </span>

                {{-- THE TASK DELETE FORM BELONGS HERE --}}
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" 
                      onsubmit="return confirm('Delete this task?')" 
                      class="opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-slate-300 hover:text-red-500 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

                <footer class="p-4 bg-slate-50/50 border-t border-slate-100">
                    <form action="{{ route('tasks.store', $project->id) }}" method="POST" class="flex flex-wrap md:flex-nowrap items-center gap-3">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        
                        <input type="text" name="title" placeholder="Add a task to this project..." required 
                               class="flex-1 min-w-[200px] bg-white border-slate-200 rounded-xl text-sm px-4 py-2 border outline-none focus:ring-2 focus:ring-indigo-500/20">

                        <select name="priority" class="bg-white border-slate-200 rounded-xl text-[10px] font-bold uppercase px-3 py-2 border outline-none cursor-pointer">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>

                        <input type="date" name="due_at" 
                               class="bg-white border-slate-200 rounded-xl text-[10px] font-bold px-3 py-2 border outline-none">

                        <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl text-xs font-bold hover:bg-indigo-600 transition-all shadow-sm">
                            Add
                        </button>
                    </form>
                </footer>
            </section>
        </div>

        {{-- Board View Section --}}
        <div x-show="viewMode === 'board'" x-cloak class="space-y-4 mb-12">
            <h2 class="text-xl font-extrabold text-slate-800 ml-2">{{ $project->name }}</h2>
            <div class="flex overflow-x-auto pb-6 gap-4 snap-x">
                @foreach(['todo' => 'To Do', 'in_progress' => 'Active', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
                    <div class="min-w-[300px] md:w-1/4 flex-shrink-0 snap-start">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</h3>
                        </div>

                        <div x-sortable 
                             data-status="{{ $status }}" 
                             class="flex flex-col gap-4 p-3 rounded-2xl bg-slate-50/50 border-2 border-dashed border-transparent min-h-[500px]">
                            
                            @foreach($project->tasks->where('status', $status) as $task)
                                <div data-id="{{ $task->id }}" 
                                     @click="openTask({{ $task->toJson() }})"
                                     class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all cursor-grab active:cursor-grabbing flex flex-col min-h-[120px]">

                                     <form action="{{ route('tasks.destroy', $task) }}" method="POST" 
          onsubmit="return confirm('Delete task?')" 
          class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
        @csrf @method('DELETE')
        <button type="submit" class="text-slate-300 hover:text-red-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </form>
                                    
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed mb-4">{{ $task->title }}</p>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50">
                                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-lg {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $task->priority }}
                                        </span>
                                        @if($task->due_at)
                                            <span class="text-[10px] font-bold uppercase tracking-tighter {{ strtolower($task->priority) === 'high' ? 'text-red-500' : 'text-indigo-500' }}">
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

    @empty
        <div class="text-center py-20 bg-white border border-dashed rounded-3xl text-slate-400 italic shadow-sm">
            <p>No projects found.</p>
        </div>
    @endforelse
</div>