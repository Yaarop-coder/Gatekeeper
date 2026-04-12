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

    // FETCH THE COMMENTS SO THEY APPEAR IN THE CHAT
    fetch(`/tasks/${task.id}/comments`)
        .then(res => res.json())
        .then(data => {
            this.activeTask.comments = data;
        });
}
}" class="relative">

    {{-- Error Alerts --}}
    @if ($errors->any())
        <div class="max-w-[98%] mx-auto px-6 mb-4">
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-3 animate-bounce">
                <span>Wait! {{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- The Main Grid --}}
    <div class="max-w-[98%] mx-auto px-2 md:px-6 grid grid-cols-12 gap-4 lg:gap-8">            
        {{-- SIDEBAR --}}
        <aside class="col-span-12 lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm sticky top-6">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6 text-center">Live Productivity</h3>
                <div class="space-y-4">
                    @foreach(['todo' => ['bg-slate-300', 'To-Do'], 'in_progress' => ['bg-indigo-500', 'Active'], 'review' => ['bg-amber-400', 'Review'], 'done' => ['bg-emerald-500', 'Completed']] as $key => $meta)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $meta[0] }}"></div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">{{ $meta[1] }}</span>
                        </div>
                        <span id="stat-{{ $key }}" class="text-sm font-black text-slate-700">{{ $stats[$key] ?? 0 }}</span>
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

        {{-- MAIN BOARD --}}
        <main class="col-span-12 lg:col-span-9 space-y-8 pb-20">
            <x-project-list :projects="$projects" />
        </main>
    </div>

    {{-- TASK DRAWER OVERLAY --}}
    <div x-show="drawerOpen" x-cloak x-transition:opacity @click="drawerOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    
    {{-- TASK DRAWER PANEL --}}
    <aside x-show="drawerOpen" x-cloak
           x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
           class="fixed right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl z-50 overflow-y-auto border-l border-slate-200">
        
        <template x-if="activeTask">
            <div class="p-8">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500" x-text="'Task ID #' + activeTask.id"></span>
                        <h2 class="text-2xl font-extrabold text-slate-800 leading-tight mt-1" x-text="activeTask.title"></h2>
                    </div>
                    <button @click="drawerOpen = false" class="text-slate-400 hover:text-slate-600 text-3xl font-light">&times;</button>
                </div>

                {{-- Task Edit Form --}}
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
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 ml-1">Due Date</label>
                            <input type="date" name="due_at" x-model="activeTask.due_at" class="w-full bg-slate-50 border rounded-xl p-3 text-sm outline-none">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold hover:bg-indigo-600 transition shadow-lg">Update Task</button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100">
    <form :action="`/tasks/${activeTask.id}`" method="POST" onsubmit="return confirm('Permanently delete this task?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors uppercase tracking-widest">
            Delete Task
        </button>
    </form>
</div>

                {{-- DISCUSSION / CHAT SECTION --}}
                <div class="mt-12 pt-10 border-t border-slate-100">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Discussion</h3>
                    
                    {{-- New Comment Form --}}
                    <form :action="`/tasks/${activeTask.id}/comments`" method="POST" class="mb-8">
                        @csrf
                        <div class="relative">
                            <textarea name="body" rows="2" required 
                                      class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 pr-12 text-sm outline-none focus:bg-white border transition-all" 
                                      placeholder="Add a comment..."></textarea>
                            <button type="submit" class="absolute bottom-3 right-3 text-indigo-600 hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            </button>
                        </div>
                    </form>

                    {{-- Comments List --}}
                    <div class="space-y-6 pb-10">
                        <template x-for="comment in activeTask.comments" :key="comment.id">
                            <div class="flex gap-3 animate-fade-in">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold border uppercase" 
                                     x-text="comment.user ? comment.user.name.split(' ').map(n => n[0]).join('').substring(0,2) : '??'">
                                </div>
                                {{-- Message Bubble --}}
                                <div class="flex-1 min-w-0">
                                    <div class="bg-slate-50 rounded-2xl p-3 px-4 inline-block max-w-full">
                                        <p class="text-[10px] font-black text-slate-800 mb-1" x-text="comment.user ? comment.user.name : 'User'"></p>
                                        <p class="text-xs text-slate-600 leading-relaxed break-words" x-text="comment.body"></p>
                                    </div>
                                    <span class="text-[9px] text-slate-400 font-medium ml-2 mt-1 block" x-text="new Date(comment.created_at).toLocaleDateString()"></span>
                                </div>
                            </div>
                        </template>
                        
                        {{-- Empty State --}}
                        <div x-show="!activeTask.comments || activeTask.comments.length === 0" class="text-center py-4">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">No messages yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </aside>
</div> 

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
    Alpine.directive('sortable', (el) => {
        let localOldStatus = ''; // Define it here

        new Sortable(el, {
            group: 'tasks', 
            animation: 150,
            ghostClass: 'bg-indigo-50',
            
            onStart: (evt) => {
                localOldStatus = evt.from.getAttribute('data-status');
            },

            onEnd: (evt) => {
    const taskId = evt.item.getAttribute('data-id');
    const newStatus = evt.to.getAttribute('data-status');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        console.error('CSRF Token missing! Add <meta name="csrf-token"> to your layout.');
        return;
    }

    if (!taskId || !newStatus || localOldStatus === newStatus) return;

    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content 
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        if (response.ok) {
            console.log('Saved successfully');
        } else {
            console.error('Save failed');
            window.location.reload(); 
        }
    })
    .catch(err => console.error('Error:', err));
}
        });
    });
});
</script>
@endpush
@endsection