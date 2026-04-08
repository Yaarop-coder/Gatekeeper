@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-12 px-6 pb-20">
    <div class="bg-white p-8 border border-slate-200 rounded-3xl shadow-sm">
        <div class="text-center mb-8">
            <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg font-black tracking-tighter text-sm">GK</span>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight mt-4">Create Account</h2>
            <p class="text-slate-400 text-sm font-medium">Join a team or start your own</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Full Name</label>
                <input type="text" name="name" :value="old('name')" required autofocus 
                    class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 p-3 border outline-none bg-slate-50/50">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Email Address</label>
                <input type="email" name="email" :value="old('email')" required 
                    class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 p-3 border outline-none bg-slate-50/50">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-1 ml-1">Company / Team</label>
                <select name="tenant_id" class="block w-full border-indigo-100 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 p-3 border outline-none bg-indigo-50/30 text-sm font-bold text-indigo-900">
                    <option value="">+ Create New Workspace</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}">Join: {{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Password</label>
                    <input type="password" name="password" required 
                        class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 p-3 border outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Confirm</label>
                    <input type="password" name="password_confirmation" required 
                        class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 p-3 border outline-none bg-slate-50/50">
                </div>
                <x-input-error :messages="$errors->get('password')" class="col-span-2 mt-1" />
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl shadow-indigo-100 uppercase text-xs tracking-widest mt-4">
                Get Started
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs font-bold text-slate-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline ml-1">
                    Sign in &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
@endsection