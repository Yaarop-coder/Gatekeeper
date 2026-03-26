@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-12">
    <div class="bg-white p-8 border border-gray-200 rounded-2xl shadow-sm">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Sign In</h2>
        
        <form action="/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3 border">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                Login to Dashboard
            </button>
        </form>
        
        <div class="mt-6 text-center text-xs text-gray-400">
            Gatekeeper Multi-Tenant Security Active
        </div>
    </div>
</div>
@endsection