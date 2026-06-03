<x-layout>
    @vite(['resources/js/pages/manual-control.js'])

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- Hero --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="h-2 w-2 rounded-full bg-[var(--color-ember)] animate-pulse"></span>
                <span class="text-xs font-mono text-gray-400 uppercase tracking-widest">Handmatig Beheer</span>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Manual Control</h1>
            <p class="text-gray-500 mt-1 text-sm">Directe motorsturing per ESP-node — De Vliegende Vlaeminck</p>
        </div>

        @php
        $espGroups = [
            [
                'key'    => 'station',
                'label'  => 'Station ESP',
                'toggle' => 'manual-switch-station',
                'border' => 'border-blue-200',
                'hbg'    => 'bg-blue-50',
                'dot'    => 'bg-blue-500',
                'text'   => 'text-blue-700',
                'motors' => [
                    ['id' => 'stationmotor',      'label' => 'Station Motor',       'actions' => ['on', 'off']],
                    ['id' => 'lifthillmotor',     'label' => 'Lifthill Motor',      'actions' => ['on', 'off']],
                    ['id' => 'stationgatesmotor', 'label' => 'Station Gates Motor', 'actions' => ['open', 'close']],
                ],
            ],
            [
                'key'    => 'tiltdrop',
                'label'  => 'Tiltdrop ESP',
                'toggle' => 'manual-switch-tiltdrop',
                'border' => 'border-amber-200',
                'hbg'    => 'bg-amber-50',
                'dot'    => 'bg-amber-500',
                'text'   => 'text-amber-700',
                'motors' => [
                    ['id' => 'tiltdropmotor',    'label' => 'Tiltdrop Motor',    'actions' => ['open', 'close']],
                    ['id' => 'releasedropmotor', 'label' => 'Releasedrop Motor', 'actions' => ['open', 'close']],
                ],
            ],
            [
                'key'    => 'brakes',
                'label'  => 'Brakes ESP',
                'toggle' => 'manual-switch-brakes',
                'border' => 'border-rose-200',
                'hbg'    => 'bg-rose-50',
                'dot'    => 'bg-rose-500',
                'text'   => 'text-rose-700',
                'motors' => [
                    ['id' => 'releasebrakesmotor', 'label' => 'Release Brakes Motor', 'actions' => ['open', 'close']],
                ],
            ],
            [
                'key'    => 'switchtrack',
                'label'  => 'Switchtrack ESP',
                'toggle' => 'manual-switch-switchtrack',
                'border' => 'border-violet-200',
                'hbg'    => 'bg-violet-50',
                'dot'    => 'bg-violet-500',
                'text'   => 'text-violet-700',
                'motors' => [
                    ['id' => 'switchtrackmotor',        'label' => 'Switchtrack Motor',         'actions' => ['brakes', 'station']],
                    ['id' => 'releaseswitchtrackmotor', 'label' => 'Release Switchtrack Motor', 'actions' => ['open', 'close']],
                ],
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6 items-start">

            {{-- Links: 2×2 grid van ESP-cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($espGroups as $group)
                    <x-esp-card :group="$group" />
                @endforeach
            </div>

            {{-- Rechts: sticky JSON-sidebar --}}
            <div class="sticky top-6 bg-gray-900 border border-gray-700 rounded-xl shadow-sm overflow-hidden">
                {{-- Tabs --}}
                <div class="flex border-b border-gray-700">
                    @foreach ($espGroups as $i => $group)
                        <button
                            class="json-tab flex-1 flex items-center justify-center gap-1 px-2 py-2.5 text-[9px] font-mono uppercase tracking-widest transition-colors duration-150 {{ $i !== 0 ? 'text-gray-500 hover:text-gray-300' : '' }}"
                            style="{{ $i === 0 ? 'color: var(--color-ember); border-bottom: 2px solid var(--color-ember)' : 'border-bottom: 2px solid transparent' }}"
                            data-target="{{ $group['key'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $group['dot'] }} shrink-0"></span>
                            {{ explode(' ', $group['label'])[0] }}
                        </button>
                    @endforeach
                </div>
                {{-- JSON panels --}}
                @foreach ($espGroups as $i => $group)
                    @php $espKey = $group['key']; $espJson = $$espKey ?? []; @endphp
                    <div id="{{ $group['key'] }}-json-panel" class="{{ $i !== 0 ? 'hidden' : '' }} p-4 overflow-auto max-h-[calc(100vh-12rem)]">
                        <pre id="{{ $group['key'] }}-json-output" class="font-mono text-[11px] text-emerald-400 leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($espJson, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

</x-layout>
