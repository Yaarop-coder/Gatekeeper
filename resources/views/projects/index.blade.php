@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-5xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-gray-400 mt-2 font-medium">Manage your projects and team activity.</p>
        </div>
        
        <form action="{{ route('projects.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="name" required placeholder="New Project Name" 
                   class="rounded-xl border-gray-300 focus:ring-indigo-500 p-3 border shadow-sm">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-6 rounded-xl transition shadow-lg active:scale-95">
                + NEW PROJECT
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-1">Total Projects</p>
            <h3 class="text-4xl font-black text-gray-900">{{ $stats['total_projects'] }}</h3>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-1">Company Tasks</p>
            <h3 class="text-4xl font-black text-gray-900">{{ $stats['total_tasks'] }}</h3>
        </div>

        <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl border border-indigo-700 transform hover:-translate-y-1 transition duration-300">
            <p class="text-indigo-100 font-bold text-xs uppercase tracking-widest mb-1">My Pending Tasks</p>
            <h3 class="text-4xl font-black text-white">{{ $stats['my_pending_tasks'] }}</h3>
            <div class="mt-2 h-1 w-12 bg-white/30 rounded"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                Active Projects
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($projects as $project)
                    <a href="{{ route('projects.show', $project->id) }}" class="group bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition">{{ $project->name }}</h4>
                            <span class="text-[10px] font-black bg-gray-50 text-gray-400 px-2 py-1 rounded uppercase">#{{ $project->id }}</span>
                        </div>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex-1 bg-gray-100 h-2 rounded-full overflow-hidden">
                                @php 
                                    $percent = $project->tasks_count > 0 ? ($project->completed_tasks_count / $project->tasks_count) * 100 : 0;
                                @endphp
                                <div class="bg-indigo-500 h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-500">{{ round($percent) }}%</span>
                        </div>

                        <div class="flex justify-between items-center text-xs font-bold text-gray-400 uppercase tracking-tighter">
                            <span>{{ $project->tasks_count }} Tasks</span>
                            <span class="text-indigo-500">{{ $project->my_tasks_count }} Assigned to you</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-2 p-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 text-center">
                        <p class="text-gray-400 font-bold italic">No projects found. Create your first one above!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <span class="w-2 h-6 bg-amber-400 rounded-full"></span>
                Recent Activity
            </h2>
            <div class="mt-8 space-y-6">
    <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
        <span class="w-2 h-6 bg-red-500 rounded-full"></span>
        My Notifications
    </h2>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <ul class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <li class="p-4 {{ $notification->read_at ? 'opacity-50' : 'bg-blue-50/30' }}">
                    <p class="text-sm font-bold text-gray-800">{{ $notification->data['message'] }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </li>
            @empty
                <li class="p-8 text-center text-gray-400 text-sm italic">No notifications yet.</li>
            @endforelse
        </ul>
        
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.read') }}" method="POST" class="p-4 bg-gray-50 text-center">
                @csrf
                <button type="submit" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>
</div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <ul class="divide-y divide-gray-50">
                    @forelse($activities as $activity)
                        <li class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-black">
                                    {{ substr($activity->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm text-gray-800">
                                        <span class="font-bold">{{ $activity->user->name }}</span> 
                                        {{ $activity->description }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-gray-400 text-sm italic">
                            No activity logged yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection