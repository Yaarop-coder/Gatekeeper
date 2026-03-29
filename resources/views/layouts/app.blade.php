<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatekeeper | {{ auth()->user()->tenant->name ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

@php
    // If we are logged in, use tenant logic. If not (Guest), use default colors.
    if (auth()->check()) {
        $tenantId = auth()->user()->tenant_id;
        $tenantName = auth()->user()->tenant->name ?? 'Portal';
        $headerClass = ($tenantId == 1) ? 'bg-slate-900' : 'bg-blue-800';
        $accentText = ($tenantId == 1) ? 'text-gray-300' : 'text-blue-200';
    } else {
        // Default style for the Login/Register pages
        $tenantName = 'Gatekeeper';
        $headerClass = 'bg-indigo-700';
        $accentText = 'text-indigo-100';
    }
@endphp

<body class="bg-gray-50 text-gray-900">

    <nav class="{{ $headerClass }} border-b border-black/10 py-4 mb-8 shadow-md">
        <div class="container mx-auto px-6 flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <h1 class="font-black text-2xl text-white tracking-tighter italic">GATEKEEPER</h1>
                <span class="text-white/30 text-xl font-thin">|</span>
                <span class="text-xs font-bold {{ $accentText }} uppercase tracking-widest">
                    {{ $tenantName }}
                </span>
            </div>
            
            <div class="flex items-center gap-6">
                <span class="text-sm font-medium text-white/90">
                    Logged in as: <span class="font-bold">{{ Auth::user()->name ?? 'Guest' }}</span>
                </span>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded transition shadow-sm">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6">
        @yield('content')
    </main>

</body>
</html>