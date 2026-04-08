@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-12 px-6">
    <div class="bg-white p-8 border border-slate-200 rounded-3xl shadow-sm">
        <div class="text-center mb-8">
            <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg font-black tracking-tighter text-sm">GK</span>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight mt-4">Welcome Back</h2>
            <p class="text-slate-400 text-sm font-medium">Please sign in to your account</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Email Address</label>
                <input type="email" name="email" :value="old('email')" required autofocus 
                    class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 border outline-none transition-all bg-slate-50/50">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Password</label>
                <input type="password" name="password" required 
                    class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 border outline-none transition-all bg-slate-50/50">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-xs font-bold text-slate-500">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl shadow-indigo-100 uppercase text-xs tracking-widest">
                {{ __('Login to Dashboard') }}
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs font-bold text-slate-500">
                New to Gatekeeper? 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline ml-1">
                    Create an account &rarr;
                </a>
            </p>
        </div>
        
        <div class="mt-6 text-center">
            <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">Multi-Tenant Security Active</span>
        </div>
    </div>
</div>
@endsection