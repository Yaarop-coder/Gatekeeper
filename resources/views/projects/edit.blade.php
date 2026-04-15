@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-black text-slate-800 mb-6 uppercase tracking-tight">Edit Project</h2>
        
        <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest block mb-2">Project Name</label>
                <input type="text" name="name" value="{{ $project->name }}" required 
                       class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 border outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest block mb-2">Update Cover Image</label>
                <input type="file" name="cover_image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white font-black py-3 rounded-xl hover:bg-indigo-700 transition-all shadow-md uppercase text-xs tracking-widest">
                    Save Changes
                </button>
                <a href="{{ route('projects.index') }}" class="flex-1 bg-slate-100 text-slate-600 text-center font-black py-3 rounded-xl hover:bg-slate-200 transition-all uppercase text-xs tracking-widest">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection