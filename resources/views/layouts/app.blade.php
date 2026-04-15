<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gatekeeper v3.5</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <div class="flex items-center gap-3 pr-6 border-r border-slate-100">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase">{{ Auth::user()->role ?? 'Member' }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md border-2 border-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group flex items-center gap-2 text-slate-400 hover:text-red-500 transition-colors">
                        <span class="text-[10px] font-black uppercase tracking-widest">Logout</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <div class="py-8">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>