<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatekeeper SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased">

    <nav class="bg-white border-b border-gray-100 py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            
            <a href="{{ route('projects.index') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-indigo-200 shadow-lg">
                    <span class="text-white font-black text-xl">G</span>
                </div>
                <span class="font-black text-xl tracking-tighter text-gray-900 uppercase">Gatekeeper</span>
            </a>

            <div class="flex items-center gap-6">
                @auth
                    <div class="relative group cursor-pointer pr-4 border-r border-gray-100">
                        <div class="flex items-center gap-1 text-gray-500 font-bold text-sm group-hover:text-indigo-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="hidden sm:inline">Alerts</span>
                        </div>

                        @php $count = auth()->user()->unreadNotifications->count(); @endphp
                        @if($count > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-black animate-bounce shadow-sm">
                                {{ $count }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-black text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1">
                                {{ auth()->user()->tenant->name ?? 'No Tenant' }}
                            </p>
                        </div>
                        
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
                        Login
                    </a>
                @endguest
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="mt-20 py-10 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">&copy; {{ date('Y') }} Gatekeeper Project Management</p>
    </footer>
<div x-data="{ 
        show: false, 
        message: '',
        type: 'success',
        display(msg, timeout = 3000) {
            this.message = msg;
            this.show = true;
            setTimeout(() => this.show = false, timeout);
        }
    }"
    @notify.window="display($event.detail.message)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-5 right-5 z-[100] pointer-events-none"
    x-cloak>
    
    <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700">
        <div class="bg-emerald-500 h-2 w-2 rounded-full animate-pulse"></div>
        <p class="text-sm font-bold tracking-wide" x-text="message"></p>
    </div>
</div>

@if(session('success'))
    <script>
        window.onload = () => {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ session('success') }}" } }));
        }
    </script>
@endif
@stack('scripts')
</body>
</html>