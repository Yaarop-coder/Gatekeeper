@extends('layouts.app')

@section('content')
<div class="container mx-auto pb-12">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active Projects</p>
                <p class="text-4xl font-black text-gray-900 mt-2">{{ $stats['total_projects'] }}</p>
            </div>
            <div class="mt-4 h-1 w-12 bg-indigo-500 rounded-full"></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Tasks</p>
                <p class="text-4xl font-black text-gray-900 mt-2">{{ $stats['total_tasks'] }}</p>
            </div>
            <div class="mt-4 h-1 w-12 bg-blue-500 rounded-full"></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Success Rate</p>
                @php
                    $rate = ($stats['total_tasks'] > 0) ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) : 0;
                @endphp
                <p class="text-4xl font-black text-indigo-600 mt-2">{{ $rate }}%</p>
            </div>
            <div class="mt-4 w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                <div class="bg-indigo-600 h-full" style="width: {{ $rate }}%"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Project Dashboard</h1>
            <p class="text-gray-500">Managing assets for <span class="font-bold text-indigo-600">{{ auth()->user()->tenant->name }}</span></p>
        </div>

        <form action="{{ route('projects.store') }}" method="POST" class="flex gap-2 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
            @csrf
            <input type="text" name="name" required placeholder="New Project Name..." 
                   class="border-none focus:ring-0 text-sm px-4 py-2 w-48 md:w-64">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-6 rounded-lg transition-all active:scale-95">
                + Create
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl border border-gray-100 rounded-2xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Project Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Progress</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Tasks</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($projects as $project)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $project->name }}</div>
                        <div class="text-xs text-gray-400">ID: #{{ $project->id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $total = $project->tasks_count;
                            $done = $project->completed_tasks_count;
                            $p = ($total > 0) ? round(($done / $total) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-full transition-all duration-500" style="width: {{ $p }}%"></div>
                            </div>
                            <span class="text-xs font-black text-gray-600">{{ $p }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase">
                            {{ $done }} / {{ $total }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end items-center gap-4">
                            <a href="{{ route('projects.show', $project->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold transition">
                                Details
                            </a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Delete this project?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                        No projects found. Create your first one above!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection