<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatekeeper v13</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-white border-b border-gray-200 py-4 mb-8">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <h1 class="font-black text-2xl text-indigo-600 tracking-tighter">GATEKEEPER</h1>
                <span class="text-gray-300">|</span>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                    Multi-Tenant Environment
                </span>
            </div>
            
            <div class="flex items-center gap-6">
                <span class="text-sm font-medium text-gray-600">
                    User: <span class="text-indigo-600">{{ Auth::user()->name ?? 'Guest' }}</span>
                </span>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-500 hover:underline">Logout</button>
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