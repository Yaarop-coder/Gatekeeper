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

    {{-- Error Alerts --}}
    @if ($errors->any())
        <div class="max-w-[98%] mx-auto px-6 mb-4">
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-3 animate-bounce">
                <span>Wait! {{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- The Main Grid --}}
    <div class="max-w-[86%] mx-auto px-2 md:px-6 grid grid-cols-12 gap-4 lg:gap-8">            
        {{-- SIDEBAR --}}
        <aside class="col-span-12 lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
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

    {{-- Task Drawer (Keep this as is) --}}
    {{-- ... rest of your drawer code ... --}}
</div>

@push('scripts')
    {{-- Your Sortable JS code --}}
@endpush
@endsection