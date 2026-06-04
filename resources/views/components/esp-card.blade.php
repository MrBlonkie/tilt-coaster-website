@props(['group'])

<div id="{{ $group['key'] }}-card" class="bg-gray-800 border {{ $group['border'] }} rounded-xl shadow-sm overflow-hidden transition-opacity duration-300">
    {{-- ESP Header --}}
    <div class="js-card-header px-5 py-2 {{ $group['hbg'] }} border-b {{ $group['border'] }} flex items-center justify-between cursor-pointer" data-esp-key="{{ $group['key'] }}">
        <div class="flex items-center gap-2.5">
            <span class="h-2 w-2 rounded-full {{ $group['dot'] }}"></span>
            <h2 class="text-xs font-bold {{ $group['text'] }} uppercase tracking-widest">{{ $group['label'] }}</h2>
            <span id="{{ $group['key'] }}-connect-dot" class="h-2 w-2 rounded-full bg-gray-600 transition-colors duration-300"></span>
            <span id="{{ $group['key'] }}-connect-status" class="text-[10px] font-mono font-bold text-gray-400">INIT...</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[9px] font-mono text-gray-400 uppercase tracking-widest">Manual</span>
            <x-toggle name="{{ $group['toggle'] }}" id="{{ $group['toggle'] }}"></x-toggle>
        </div>
    </div>
    {{-- Motors --}}
    <div id="{{ $group['key'] }}-motor-controls" class="divide-y divide-gray-700 transition-opacity duration-200 opacity-40 pointer-events-none">
        @foreach ($group['motors'] as $motor)
        <div class="p-3.5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-mono text-gray-400 uppercase tracking-wide">{{ $motor['label'] }}</span>
                <span id="{{ $motor['id'] }}-status" class="text-[10px] font-mono font-bold text-gray-400 px-2 py-0.5 bg-gray-700 rounded-full">UNKNOWN</span>
            </div>
            <div class="flex gap-2">
                @if ($motor['actions'][0] === 'on')
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-red-900/20 hover:bg-red-600 text-red-400 hover:text-white
                        border border-red-700/50 hover:border-red-600 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="off">
                        {{ strtoupper($motor['actions'][1]) }}
                    </button>
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-emerald-900/20 hover:bg-emerald-600 text-emerald-400 hover:text-white
                        border border-emerald-700/50 hover:border-emerald-600 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="on">
                        {{ strtoupper($motor['actions'][0]) }}
                    </button>
                @elseif ($motor['actions'][0] === 'brakes')
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-gray-700/50 hover:bg-gray-600 text-gray-400 hover:text-white
                        border border-gray-600 hover:border-gray-500 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="on">
                        {{ strtoupper($motor['actions'][0]) }}
                    </button>
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-blue-900/20 hover:bg-blue-600 text-blue-400 hover:text-white
                        border border-blue-700/50 hover:border-blue-600 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="off">
                        {{ strtoupper($motor['actions'][1]) }}
                    </button>
                @else
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-gray-700/50 hover:bg-gray-600 text-gray-400 hover:text-white
                        border border-gray-600 hover:border-gray-500 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="off">
                        {{ strtoupper($motor['actions'][1]) }}
                    </button>
                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                        bg-blue-900/20 hover:bg-blue-600 text-blue-400 hover:text-white
                        border border-blue-700/50 hover:border-blue-600 transition-all duration-150 active:scale-[0.97]"
                        data-target="{{ $motor['id'] }}" data-action="on">
                        {{ strtoupper($motor['actions'][0]) }}
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
