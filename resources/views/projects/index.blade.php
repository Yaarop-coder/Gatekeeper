@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Projects</h2>
        <p class="text-gray-500 text-sm">
            Logged in as: <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span> 
            (Tenant: {{ auth()->user()->tenant->name ?? 'Default' }})
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 shadow-sm rounded-r-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-10 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Create New Project</h3>
        <form action="{{ route('projects.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="name" 
                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border" 
                   placeholder="Enter project name..." required>
            <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out">
                + Add Project
            </button>
        </form>
    </div>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Project Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $project->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">
                            No projects found for this tenant. The Gatekeeper is working!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection