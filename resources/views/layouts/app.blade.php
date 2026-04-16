<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gatekeeper v3.5</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] antialiased">

    {{-- MODERN NAV HEADER --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-3">
        <div class="max-w-[1600px] mx-auto flex items-center justify-between">
            
            {{-- Left: Logo --}}
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-indigo-200 shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-xl font-black text-slate-800 tracking-tighter">Gatekeeper <span class="text-indigo-600">v3.5</span></span>
            </div>

            {{-- Right: User Profile & Logout --}}
            <div class="flex items-center gap-6">
    @auth
        {{-- Move your User Profile & Logout code inside here --}}
        <div class="flex items-center gap-3 pr-6 border-r border-slate-100">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] font-bold text-indigo-500 uppercase">{{ Auth::user()->role }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md border-2 border-white">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>
        {{-- Language Switcher --}}
<div class="flex items-center gap-2 px-4 border-r border-slate-100 mr-4">
    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-indigo-600 font-black' : 'text-slate-400' }} text-[10px] tracking-widest">EN</a>
    <span class="text-slate-200">|</span>
    <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'text-indigo-600 font-black' : 'text-slate-400' }} text-[10px] tracking-widest font-arabic">عربي</a>
</div>

{{-- Logout Form --}}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="group flex items-center gap-2 text-slate-400 hover:text-red-500">
        <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Logout') }}</span>
    </button>
</form>
    @else
        {{-- Show this if they are NOT logged in (Registration/Login pages) --}}
        <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600">Login</a>
        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md">Join GK</a>
    @endauth
</div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <div class="py-8">
        @yield('content')
    </div>

    @stack('scripts')

    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-3"></div>

<template id="toast-template">
    <div class="flex items-center gap-3 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl transform translate-y-10 opacity-0 transition-all duration-500 ease-out border border-white/10">
        <div class="bg-green-500 p-1 rounded-full">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-xs font-bold tracking-wide uppercase">Task Updated</p>
    </div>
</template>
</body>
</html>