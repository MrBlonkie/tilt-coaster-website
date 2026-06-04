<x-layout>
    @vite(['resources/js/pages/home.js'])

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- HERO --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="h-2 w-2 rounded-full bg-[var(--color-ember)] animate-pulse"></span>
                <span class="text-xs font-mono text-gray-400 uppercase tracking-widest">Live systeem</span>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Dashboard</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Rij 1: CTA (col 1) + Log (col 2-3) --}}

            {{-- CTA --}}
            <div class="flex flex-col bg-gray-800 border border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-700 shrink-0">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Bediening</h2>
                </div>
                <div class="flex-1 flex flex-col justify-center p-4">
                <div class="grid grid-cols-2 gap-3">
                    <a href="/auto-control"
                        class="flex flex-col items-center gap-2.5 p-4 bg-[var(--color-ember)]/15 hover:bg-[var(--color-ember)]/25 border border-[var(--color-ember)]/30 rounded-xl transition-all text-center group">
                        <div class="p-2.5 bg-[var(--color-ember)] rounded-lg group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white">Auto</div>
                            <div class="text-[11px] text-gray-400 leading-tight mt-0.5">Volledig automatisch</div>
                        </div>
                    </a>
                    <a href="/manual-control"
                        class="flex flex-col items-center gap-2.5 p-4 bg-gray-700/50 hover:bg-gray-700 border border-gray-600 rounded-xl transition-all text-center group">
                        <div class="p-2.5 bg-gray-600 rounded-lg group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white">Manueel</div>
                            <div class="text-[11px] text-gray-400 leading-tight mt-0.5">Handmatige besturing</div>
                        </div>
                    </a>
                </div>
                </div>
            </div>

            {{-- Event Log --}}
            <div class="lg:col-span-2 flex flex-col bg-gray-800 border border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-700 flex items-center gap-3 shrink-0">
                    <div class="flex gap-1.5">
                        <div class="h-3 w-3 rounded-full bg-red-400"></div>
                        <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                        <div class="h-3 w-3 rounded-full bg-green-400"></div>
                    </div>
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Systeem Log</h2>
                    <span class="ml-auto text-xs font-mono text-gray-400" id="log-count">0 events</span>
                </div>
                <div class="flex-1 flex flex-col p-3 bg-gray-900 min-h-0">
                    <ul id="event-log-list" class="flex-1 space-y-px overflow-y-auto font-mono text-xs pr-1 min-h-[100px]">
                        <li class="py-1 px-2 text-gray-500">Wachten op verbinding...</li>
                    </ul>
                </div>
            </div>

            {{-- Rij 2: Systeem Status (col 1) + Demo video (col 2-3) --}}

            {{-- Systeem Status --}}
            <div class="flex flex-col bg-gray-800 border border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-700 flex items-center justify-between shrink-0">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Systeem Status</h2>
                    <span class="text-xs font-mono text-gray-500">ESP NODES</span>
                </div>
                <div class="p-4 flex flex-col flex-1 space-y-4 overflow-y-auto">

                    {{-- MQTT verbinding --}}
                    <div class="space-y-1.5 pb-3 border-b border-gray-700 shrink-0">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-mono text-gray-400">MQTT broker</span>
                            <span id="mqtt-broker-status" class="text-xs font-mono font-bold text-gray-400">CONNECTING...</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-mono text-gray-500">Connected since</span>
                            <span id="mqtt-connected-since" class="text-xs font-mono text-gray-400">...</span>
                        </div>
                    </div>

                    {{-- Nodes --}}
                    <div class="space-y-5">

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Station ESP</span>
                                    <span id="last-hb-station" class="text-[10px] font-mono text-gray-500">...</span>
                                </div>
                                <span id="station-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
                            </div>
                            <canvas id="station-monitor" height="48" class="w-full rounded bg-gray-900 border border-gray-700"></canvas>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Tiltdrop ESP</span>
                                    <span id="last-hb-tiltdrop" class="text-[10px] font-mono text-gray-500">...</span>
                                </div>
                                <span id="tiltdrop-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
                            </div>
                            <canvas id="tiltdrop-monitor" height="48" class="w-full rounded bg-gray-900 border border-gray-700"></canvas>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Brakes ESP</span>
                                    <span id="last-hb-brakes" class="text-[10px] font-mono text-gray-500">...</span>
                                </div>
                                <span id="brakes-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
                            </div>
                            <canvas id="brakes-monitor" height="48" class="w-full rounded bg-gray-900 border border-gray-700"></canvas>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Switchtrack ESP</span>
                                    <span id="last-hb-switchtrack" class="text-[10px] font-mono text-gray-500">...</span>
                                </div>
                                <span id="switchtrack-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
                            </div>
                            <canvas id="switchtrack-monitor" height="48" class="w-full rounded bg-gray-900 border border-gray-700"></canvas>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="lg:col-span-2 flex flex-col bg-gray-800 border border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-700 shrink-0">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Media</h2>
                </div>
                <iframe
                    class="flex-1 min-h-0 w-full"
                    src="https://www.youtube.com/embed/zCFXWvgAUE4"
                    title="De Vliegende Vlaeminck Build Journey"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>

        </div>
    </div>

</x-layout>
