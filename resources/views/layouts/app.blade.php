<!DOCTYPE html>
<html lang="en">
<head>
    {{-- ... your meta tags and vite scripts ... --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc]">
    {{-- PERMANENT HEADER --}}
    <nav class="bg-white border-b border-slate-200 px-8 py-4 mb-8">
        <div class="max-w-[98%] mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="bg-indigo-600 text-white p-2 rounded-lg font-black tracking-tighter text-xl shadow-sm">GK</span>
                <h1 class="text-xl font-bold tracking-tight text-slate-800">Gatekeeper <span class="text-slate-400 font-medium text-sm ml-2 italic">v3.5</span></h1>
            </div>
            
            <div class="flex items-center gap-6">
                <form action="{{ route('projects.store') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Start new project..." required class="bg-slate-50 border-slate-200 rounded-full text-sm px-4 py-2 w-48 border outline-none">
                    <button type="submit" class="bg-slate-900 text-white h-9 w-9 rounded-full flex items-center justify-center hover:bg-indigo-600 transition-all">+</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-widest">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT INJECTION --}}
    @yield('content')

    @stack('scripts')
</body>
</html>