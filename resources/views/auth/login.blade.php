<x-guest-layout>
    {{-- We removed @extends and @section --}}
    {{-- We also removed the outer div and the white card div because x-guest-layout already provides them --}}
    
    <div class="text-center mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight mt-4">Welcome Back</h2>
        <p class="text-slate-400 text-sm font-medium">Please sign in to your account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        {{-- ... rest of your form fields ... --}}
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 border outline-none transition-all bg-slate-50/50">
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Password</label>
            <input type="password" name="password" required 
                class="block w-full border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 border outline-none transition-all bg-slate-50/50">
        </div>
        
        {{-- ... button and footer ... --}}
        <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl shadow-indigo-100 uppercase text-xs tracking-widest">
            {{ __('Sign In') }}
        </button>
    </form>
</x-guest-layout>