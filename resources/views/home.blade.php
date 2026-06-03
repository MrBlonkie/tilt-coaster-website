<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    @vite(['resources/js/pages/home.js'])

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- HERO --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="h-2 w-2 rounded-full bg-[var(--color-ember)] animate-pulse"></span>
                <span class="text-xs font-mono text-gray-500 uppercase tracking-widest">Live systeem</span>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Dashboard</h1>
            <p class="text-gray-500 mt-1 text-sm">Rollercoaster besturingscentrum — De Vliegende Vlaeminck</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM 1: NAVIGATIE & SYSTEEM STATUS --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Quick Actions --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800">
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Bediening</h2>
                    </div>
                    <div class="p-4 space-y-2.5">
                        <a href="/auto-control"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-[var(--color-primary)] hover:bg-[var(--color-ember)] text-white font-semibold rounded-lg text-sm transition-all duration-200 group">
                            <svg class="h-4 w-4 shrink-0 text-[var(--color-ember)] group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Auto Control
                        </a>
                        <a href="/manual-control"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-[var(--color-accent)] hover:bg-[var(--color-highlight)] text-white font-semibold rounded-lg text-sm transition-all duration-200">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            Manual Control
                        </a>
                    </div>
                </div>

                {{-- Systeem Status --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Systeem Status</h2>
                        <span class="text-xs font-mono text-gray-600">ESP NODES</span>
                    </div>
                    <div class="p-4 space-y-5">

                        {{-- Station Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Station</span>
                                <span id="station-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="station-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Tiltdrop Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Tiltdrop</span>
                                <span id="tiltdrop-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="tiltdrop-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Brakes Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Brakes</span>
                                <span id="brakes-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="brakes-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Switchtrack Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Switchtrack</span>
                                <span id="switchtrack-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="switchtrack-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                    </div>
                </div>

            </div>

            {{-- KOLOM 2: EVENT LOG --}}
            <div class="lg:col-span-2">
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                    {{-- Terminal header --}}
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <div class="h-3 w-3 rounded-full bg-red-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-yellow-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-green-500/70"></div>
                        </div>
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Systeem Log</h2>
                        <span class="ml-auto text-xs font-mono text-gray-600" id="log-count">0 events</span>
                    </div>
                    <div class="p-3 flex-1">
                        <ul id="event-log-list" class="space-y-px h-[560px] overflow-y-auto font-mono text-xs pr-1">
                            <li class="py-1 px-2 text-gray-600">Wachten op verbinding...</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-layout>
