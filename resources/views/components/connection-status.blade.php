@props(['title' => 'Connectie Status'])

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ $title }}</h2>
        <span class="text-xs font-mono text-gray-400">MQTT</span>
    </div>
    <div class="divide-y divide-gray-100">
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-xs font-mono text-gray-600 uppercase tracking-wide">Station ESP</span>
            <div class="flex items-center gap-2">
                <span id="station-connect-dot" class="h-2 w-2 rounded-full bg-gray-300 transition-colors duration-300"></span>
                <span id="station-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-xs font-mono text-gray-600 uppercase tracking-wide">Tiltdrop ESP</span>
            <div class="flex items-center gap-2">
                <span id="tiltdrop-connect-dot" class="h-2 w-2 rounded-full bg-gray-300 transition-colors duration-300"></span>
                <span id="tiltdrop-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-xs font-mono text-gray-600 uppercase tracking-wide">Brakes ESP</span>
            <div class="flex items-center gap-2">
                <span id="brakes-connect-dot" class="h-2 w-2 rounded-full bg-gray-300 transition-colors duration-300"></span>
                <span id="brakes-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-xs font-mono text-gray-600 uppercase tracking-wide">Switchtrack ESP</span>
            <div class="flex items-center gap-2">
                <span id="switchtrack-connect-dot" class="h-2 w-2 rounded-full bg-gray-300 transition-colors duration-300"></span>
                <span id="switchtrack-connect-status" class="text-xs font-mono font-bold text-gray-400">INIT...</span>
            </div>
        </div>
    </div>
</div>
