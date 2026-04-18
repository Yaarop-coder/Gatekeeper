<div x-data="{
    search: '',
    viewMode: 'list',
    showCompleted: true,
    filterPriority: 'all',
    filterUrgency: 'all'
}" class="space-y-8">

    {{-- Filter Header (Kept as is) --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input x-model="search" type="text" placeholder="{{ __('Search tasks') }}..."
                    class="block w-full pl-10 pr-3 py-2 border border-slate-100 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div class="flex items-center gap-3">
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">{{ __('List') }}</button>
                    <button @click="viewMode = 'board'"
                        :class="viewMode === 'board' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">{{ __('Board') }}</button>
                </div>
                <button @click="showCompleted = !showCompleted"
                    :class="showCompleted ? 'bg-indigo-50 text-indigo-600 border-indigo-100' :
                        'bg-slate-50 text-slate-400 border-slate-100'"
                    class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                    <span x-text="showCompleted ? '{{ __('Showing All') }}' : '{{ __('Active Only') }}'"></span>
                </button>
            </div>
        </div>
    </div>

    @forelse($projects as $project)
        @php
            $total = $project->tasks->count();
            $completed = $project->tasks->where('status', 'done')->count();
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp

        {{-- List View Section --}}
        <div x-show="viewMode === 'list'">
            <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">

                {{-- PROJECT BANNER --}}
                <div class="h-48 w-full bg-slate-200 relative">
                    @if ($project->cover_image)
                        {{-- The 'storage/' part is mandatory because of the symlink --}}
                        <img src="{{ asset('storage/' . $project->cover_image) }}" class="w-full h-full object-cover"
                            alt="Project Cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                            <span>No Image</span>
                        </div>
                    @endif
                </div>

                <header class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $project->name }}</h2>
                            @can('delete', $project)
                                <div class="flex items-center gap-3">
                                    {{-- EDIT BUTTON --}}
                                    <a href="{{ route('projects.edit', $project) }}"
                                        class="p-2 bg-slate-100 hover:bg-indigo-100 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    {{-- PDF BUTTON --}}
                                    <a href="{{ route('projects.export', $project) }}"
                                        class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 2l3.5 3.5H11V4z" />
                                        </svg>
                                        {{ __('Export PDF') }}
                                    </a>

                                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                        onsubmit="return confirm('Delete?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] font-bold text-red-400 hover:text-red-600 uppercase tracking-widest">
                                            {{ __('Delete Project') }}
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex flex-col items-end gap-1">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('Progress') }}</span>
                                    <span class="text-sm font-black text-indigo-600">{{ $percent }}%</span>
                                </div>
                                <div class="w-48 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-1000"
                                        style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            {{-- RESTORED TASK COUNT --}}
                            <div class="hidden md:flex flex-col items-center px-4 border-l border-slate-200">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('Tasks') }}</span>
                                <span
                                    class="text-sm font-black text-slate-700">{{ $completed }}/{{ $total }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="divide-y divide-slate-50">
                    @foreach ($project->tasks as $task)
                        <div
                            class="group flex items-center justify-between p-4 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition-colors">
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-[10px] font-black uppercase px-2 py-1 rounded bg-slate-100 text-slate-500">{{ __(str_replace('_', ' ', strtoupper($task->status))) }}</span>
                                <h4 @click="openTask({{ $task->toJson() }})"
                                    class="text-sm font-bold text-slate-700 cursor-pointer">{{ $task->title }}</h4>
                            </div>

                            <div class="flex items-center gap-6">
                                {{-- RESTORED DUE DATE --}}
                                @if ($task->due_at)
                                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">
                                        {{ \Carbon\Carbon::parse($task->due_at)->translatedFormat('d M') }}
                                    </span>
                                @endif

                                <span
                                    class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' }}">
                                    {{ __($task->priority) }}
                                </span>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                    onsubmit="return confirm('Delete task?')"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-red-500 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <footer class="p-4 bg-slate-50/50 border-t border-slate-100">
                    <form action="{{ route('tasks.store', $project->id) }}" method="POST"
                        enctype="multipart/form-data" class="flex flex-wrap md:flex-nowrap items-center gap-3">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <input type="text" name="title" placeholder="{{ __('Add a task') }}..." required
                            class="flex-1 min-w-[200px] bg-white border-slate-200 rounded-xl text-sm px-4 py-2 border outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <select name="priority"
                            class="bg-white border-slate-200 rounded-xl text-[10px] font-bold uppercase px-3 py-2 border outline-none cursor-pointer">
                            <option value="low">{{ __('Low') }}</option>
                            <option value="medium" selected>{{ __('Medium') }}</option>
                            <option value="high">{{ __('High') }}</option>
                        </select>
                        <input type="text" name="due_at" onfocus="(this.type='date')"
                            onblur="(this.value ? this.type='date' : this.type='text')"
                            placeholder="{{ __('Due Date') }}"
                            class="bg-white border-slate-200 rounded-xl text-[10px] font-bold px-3 py-2 border outline-none placeholder:text-slate-400">
                        <button type="submit"
                            class="bg-slate-900 text-white px-6 py-2 rounded-xl text-xs font-bold hover:bg-indigo-600 transition-all shadow-sm">{{ __('Add') }}</button>
                    </form>
                </footer>
            </section>
        </div>

        {{-- Board View --}}
        <div x-show="viewMode === 'board'" x-cloak class="space-y-4 mb-12">
            {{-- Board Header with Progress Bar --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 ml-2 mb-6">
                <div class="flex items-center gap-3">
                    @if ($project->cover_image)
                        <img src="{{ asset('storage/' . $project->cover_image) }}"
                            class="w-8 h-8 rounded-lg object-cover">
                    @endif
                    <h2 class="text-xl font-extrabold text-slate-800">{{ $project->name }}</h2>
                </div>

                {{-- Interactive Progress Bar --}}
                <div class="flex items-center gap-6">
                    <div class="flex flex-col items-end gap-1">
                        <div class="flex items-center gap-3">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400">Progress</span>
                            <span class="text-sm font-black text-indigo-600">{{ $percent }}%</span>
                        </div>
                        <div class="w-48 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            {{-- The JavaScript targets the bg-indigo-500 class below --}}
                            <div class="h-full bg-indigo-500 transition-all duration-500"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex overflow-x-auto pb-6 gap-4 snap-x">
                @foreach (['todo' => 'To Do', 'in_progress' => 'Active', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
                    <div class="min-w-[300px] md:w-1/4 flex-shrink-0 snap-start">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __($label) }}</h3>
                        </div>
                        {{-- The JavaScript targets the x-sortable and data-status below --}}
                        <div x-sortable data-status="{{ $status }}"
                            class="flex flex-col gap-4 p-3 rounded-2xl bg-slate-50/50 min-h-[500px]">
                            @foreach ($project->tasks->where('status', $status) as $task)
                                <div data-id="{{ $task->id }}" @click="openTask({{ $task->toJson() }})"
                                    class="group relative bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all cursor-grab active:cursor-grabbing flex flex-col min-h-[120px]">
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                        onsubmit="return confirm('Delete task?')"
                                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-300 hover:text-red-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed mb-4">
                                        {{ $task->title }}</p>
                                    <div
                                        class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50">
                                        <span
                                            class="text-[9px] font-black uppercase px-2 py-1 rounded-lg {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                            {{ __(ucfirst($task->priority ?? 'Medium')) }}
                                        </span>
                                        @if ($task->due_at)
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-tighter {{ $task->priority === 'high' ? 'text-red-500' : 'text-indigo-500' }}">
                                                {{ \Carbon\Carbon::parse($task->due_at)->format('d M') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white border border-dashed rounded-3xl text-slate-400 italic">
            <p>No projects found.</p>
        </div>
    @endforelse
</div>
{{-- Load SortableJS from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Drag and Drop
        document.querySelectorAll('[x-sortable]').forEach(el => {
            new Sortable(el, {
                group: 'tasks',
                animation: 150,
                ghostClass: 'bg-indigo-50',
                onEnd: function(evt) {
                    let taskId = evt.item.getAttribute('data-id');
                    let newStatus = evt.to.getAttribute('data-status');

                    updateTaskStatus(taskId, newStatus);
                    updateLocalProgressBar(evt.item.closest('.space-y-4'));
                    updateGlobalStats();

                    if (newStatus === 'done') {
                        fireConfetti();
                    }
                }
            });
        });
    });

    // 1. Sync with Laravel
    function updateTaskStatus(id, status) {
        fetch(`/tasks/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => {
                if (response.ok) showToast();
            });
    }

    // 2. Global Productivity Calculation (Sidebar & Chart)
    function updateGlobalStats() {
        const statuses = ['todo', 'in_progress', 'review', 'done'];
        let doneCount = 0;
        let pendingCount = 0;

        statuses.forEach(status => {
            let count = document.querySelectorAll(`[data-status="${status}"] [data-id]`).length;

            // Sidebar Update with Animation
            let element = document.getElementById(`stat-${status}`);
            if (element) {
                let start = parseInt(element.innerText) || 0;
                if (start !== count) {
                    animateNumber(element, start, count, 400);
                }
            }

            if (status === 'done') doneCount = count;
            else pendingCount += count;
        });

        if (window.globalTaskChart) {
            window.globalTaskChart.data.datasets[0].data = [doneCount, pendingCount];
            window.globalTaskChart.update();
        }
    }

    // 3. Local Project Progress Bar
    function updateLocalProgressBar(container) {
        if (!container) return;
        let allTasks = container.querySelectorAll('[data-id]').length;
        let doneTasks = container.querySelector('[data-status="done"]').querySelectorAll('[data-id]').length;
        let percent = allTasks > 0 ? Math.round((doneTasks / allTasks) * 100) : 0;

        let bar = container.querySelector('.bg-indigo-500');
        let text = container.querySelector('.text-indigo-600');
        if (bar) bar.style.width = percent + '%';
        if (text) text.innerText = percent + '%';
    }

    // 4. Smooth Number Ticker
    function animateNumber(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerText = Math.floor(progress * (end - start) + start);
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    }

    // 5. Success Toast
    function showToast() {
        const container = document.getElementById('toast-container');
        const template = document.getElementById('toast-template');
        if (!container || !template) return;

        const clone = template.content.cloneNode(true);
        const toast = clone.querySelector('div');
        container.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // 6. Celebration Confetti
    function fireConfetti() {
        const duration = 1500;
        const animationEnd = Date.now() + duration;
        const defaults = {
            startVelocity: 30,
            spread: 360,
            ticks: 60,
            zIndex: 9999
        };

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 60 * (timeLeft / duration);

            confetti({
                ...defaults,
                particleCount,
                origin: {
                    x: 0.2,
                    y: 0.7
                }
            });
            confetti({
                ...defaults,
                particleCount,
                origin: {
                    x: 0.8,
                    y: 0.7
                }
            });

        }, 500);
    }
</script>
