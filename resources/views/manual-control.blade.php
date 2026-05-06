<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- Hero --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="h-2 w-2 rounded-full bg-[var(--color-ember)] animate-pulse"></span>
                <span class="text-xs font-mono text-gray-500 uppercase tracking-widest">Handmatig Beheer</span>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Manual Control</h1>
            <p class="text-gray-500 mt-1 text-sm">Directe motorsturing per ESP-node — De Vliegende Vlaeminck</p>
        </div>

        {{-- Manual Mode Toggles --}}
        <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-5 py-3.5 border-b border-gray-800">
                <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Manual Mode Schakelaars</h2>
            </div>
            <div class="p-4 flex flex-row flex-wrap items-center gap-6 text-gray-300 font-semibold">
                <x-toggle name="manual-switch-station" id="manual-switch-station">STATION</x-toggle>
                <x-toggle name="manual-switch-tiltdrop" id="manual-switch-tiltdrop">TILTDROP</x-toggle>
                <x-toggle name="manual-switch-brakes" id="manual-switch-brakes">BRAKES</x-toggle>
                <x-toggle name="manual-switch-switchtrack" id="manual-switch-switchtrack">SWITCHTRACK</x-toggle>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- LEFT COLUMN: Status & JSON --}}
            <div class="space-y-6">

                {{-- Connection Status --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Connectie Status</h2>
                        <span class="text-xs font-mono text-gray-600">MQTT</span>
                    </div>
                    <div class="divide-y divide-gray-800">
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Station ESP</span>
                            <span id="station-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Tiltdrop ESP</span>
                            <span id="tiltdrop-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Brakes ESP</span>
                            <span id="brakes-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-xs font-mono text-gray-400 uppercase tracking-wide">Switchtrack ESP</span>
                            <span id="switchtrack-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                        </div>
                    </div>
                </div>

                {{-- JSON Dumps --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <div class="h-3 w-3 rounded-full bg-red-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-yellow-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-green-500/70"></div>
                        </div>
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Live JSON Data</h2>
                    </div>
                    <div class="p-4 bg-black/30 space-y-4 overflow-x-auto">
                        <div>
                            <p class="text-[10px] font-mono text-[var(--color-ember)] uppercase tracking-widest mb-1">STATION:</p>
                            <pre id="station-json-output" class="font-mono text-[11px] text-emerald-400 leading-relaxed">{{ json_encode($station, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <p class="text-[10px] font-mono text-[var(--color-ember)] uppercase tracking-widest mb-1">TILTDROP:</p>
                            <pre id="tiltdrop-json-output" class="font-mono text-[11px] text-emerald-400 leading-relaxed">{{ json_encode($tiltdrop, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <p class="text-[10px] font-mono text-[var(--color-ember)] uppercase tracking-widest mb-1">BRAKES:</p>
                            <pre id="brakes-json-output" class="font-mono text-[11px] text-emerald-400 leading-relaxed">{{ json_encode($brakes, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <p class="text-[10px] font-mono text-[var(--color-ember)] uppercase tracking-widest mb-1">SWITCHTRACK:</p>
                            <pre id="switchtrack-json-output" class="font-mono text-[11px] text-emerald-400 leading-relaxed">{{ json_encode($switchtrack ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Motor Controls --}}
            <div class="space-y-4">

                @php
                $espGroups = [
                    [
                        'key'    => 'station',
                        'label'  => 'Station ESP',
                        'border' => 'border-blue-900/40',
                        'hbg'    => 'bg-blue-950/30',
                        'dot'    => 'bg-blue-500',
                        'text'   => 'text-blue-300',
                        'motors' => [
                            ['id' => 'stationmotor',      'label' => 'Station Motor',       'actions' => ['on', 'off']],
                            ['id' => 'lifthillmotor',     'label' => 'Lifthill Motor',      'actions' => ['on', 'off']],
                            ['id' => 'stationgatesmotor', 'label' => 'Station Gates Motor', 'actions' => ['open', 'close']],
                        ],
                    ],
                    [
                        'key'    => 'tiltdrop',
                        'label'  => 'Tiltdrop ESP',
                        'border' => 'border-amber-900/40',
                        'hbg'    => 'bg-amber-950/30',
                        'dot'    => 'bg-amber-500',
                        'text'   => 'text-amber-300',
                        'motors' => [
                            ['id' => 'tiltdropmotor',    'label' => 'Tiltdrop Motor',    'actions' => ['open', 'close']],
                            ['id' => 'releasedropmotor', 'label' => 'Releasedrop Motor', 'actions' => ['open', 'close']],
                        ],
                    ],
                    [
                        'key'    => 'brakes',
                        'label'  => 'Brakes ESP',
                        'border' => 'border-rose-900/40',
                        'hbg'    => 'bg-rose-950/30',
                        'dot'    => 'bg-rose-500',
                        'text'   => 'text-rose-300',
                        'motors' => [
                            ['id' => 'releasebrakesmotor', 'label' => 'Release Brakes Motor', 'actions' => ['open', 'close']],
                        ],
                    ],
                    [
                        'key'    => 'switchtrack',
                        'label'  => 'Switchtrack ESP',
                        'border' => 'border-violet-900/40',
                        'hbg'    => 'bg-violet-950/30',
                        'dot'    => 'bg-violet-500',
                        'text'   => 'text-violet-300',
                        'motors' => [
                            ['id' => 'switchtrackmotor',        'label' => 'Switchtrack Motor',         'actions' => ['brakes', 'station']],
                            ['id' => 'releaseswitchtrackmotor', 'label' => 'Release Switchtrack Motor', 'actions' => ['open', 'close']],
                        ],
                    ],
                ];
                @endphp

                @foreach ($espGroups as $group)
                <div class="bg-[var(--color-primary)] border {{ $group['border'] }} rounded-xl shadow-lg overflow-hidden">
                    {{-- ESP Header --}}
                    <div class="px-5 py-3 {{ $group['hbg'] }} border-b {{ $group['border'] }} flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="h-2 w-2 rounded-full {{ $group['dot'] }}"></span>
                            <h2 class="text-xs font-bold {{ $group['text'] }} uppercase tracking-widest">{{ $group['label'] }}</h2>
                        </div>
                        <span class="text-[10px] font-mono text-gray-600">
                            {{ count($group['motors']) }} {{ count($group['motors']) === 1 ? 'motor' : 'motors' }}
                        </span>
                    </div>
                    {{-- Motors --}}
                    <div class="divide-y divide-gray-800/50">
                        @foreach ($group['motors'] as $motor)
                        <div class="p-3.5">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[11px] font-mono text-gray-400 uppercase tracking-wide">{{ $motor['label'] }}</span>
                                <span id="{{ $motor['id'] }}-status" class="text-[10px] font-mono font-bold text-gray-600 px-2 py-0.5 bg-black/30 rounded-full">LADEN...</span>
                            </div>
                            <div class="flex gap-2">
                                @if ($motor['actions'][0] === 'on')
                                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                                        bg-emerald-900/40 hover:bg-emerald-700/70 text-emerald-400 hover:text-white
                                        border border-emerald-800/50 hover:border-emerald-500 transition-all duration-150 active:scale-[0.97]"
                                        data-target="{{ $motor['id'] }}" data-action="on">
                                        {{ strtoupper($motor['actions'][0]) }}
                                    </button>
                                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                                        bg-red-900/40 hover:bg-red-700/70 text-red-400 hover:text-white
                                        border border-red-800/50 hover:border-red-500 transition-all duration-150 active:scale-[0.97]"
                                        data-target="{{ $motor['id'] }}" data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </button>
                                @else
                                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                                        bg-slate-800/50 hover:bg-slate-700 text-slate-300 hover:text-white
                                        border border-slate-700/50 hover:border-slate-500 transition-all duration-150 active:scale-[0.97]"
                                        data-target="{{ $motor['id'] }}" data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </button>
                                    <button class="js-esp-button flex-1 flex items-center justify-center py-2.5 px-4 text-xs font-bold font-mono uppercase tracking-widest rounded-lg
                                        bg-[var(--color-accent)]/20 hover:bg-[var(--color-accent)]/60 text-[var(--color-highlight)] hover:text-white
                                        border border-[var(--color-accent)]/40 hover:border-[var(--color-accent)] transition-all duration-150 active:scale-[0.97]"
                                        data-target="{{ $motor['id'] }}" data-action="on">
                                        {{ strtoupper($motor['actions'][0]) }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>

        </div>
    </div>

    {{-- ====================== JS =========================== --}}
    <script>
        const MQTT_HOST = "{{ env('PI_IP') }}";
        const client = mqtt.connect(`ws://${MQTT_HOST}:9001`);

        const currentStatus = {
            station: {},
            tiltdrop: {},
            brakes: {},
            switchtrack: {},
            stationLwt: '{{ $stationOnline }}',
            tiltdropLwt: '{{ $tiltdropOnline }}',
            brakesLwt: '{{ $brakesOnline }}',
            switchtrackLwt: '{{ $switchtrackOnline }}'
        };

        const HEARTBEAT_TIMEOUT_MS = 4500;
        const HEARTBEAT_TIMERS = {
            station: null,
            tiltdrop: null,
            brakes: null,
            switchtrack: null
        };

        function setConnectStatus(device, status) {
            currentStatus[`${device}Lwt`] = status;
            const el = document.getElementById(`${device}-connect-status`);
            if (!el) return;

            el.classList.remove('text-emerald-400', 'text-red-400', 'text-gray-600');
            if (status === 'online') {
                el.classList.add('text-emerald-400');
                el.innerText = 'ONLINE';
            } else if (status === 'offline') {
                el.classList.add('text-red-400');
                el.innerText = 'OFFLINE';
            } else {
                el.classList.add('text-gray-600');
                el.innerText = 'INIT...';
            }
        }

        function resetHeartbeatTimer(device) {
            if (HEARTBEAT_TIMERS[device]) clearTimeout(HEARTBEAT_TIMERS[device]);
            setConnectStatus(device, 'online');

            HEARTBEAT_TIMERS[device] = setTimeout(() => {
                setConnectStatus(device, 'offline');
            }, HEARTBEAT_TIMEOUT_MS);
        }

        function updateJsonUI() {
            document.getElementById('station-json-output').innerText = JSON.stringify(currentStatus.station, null, 2);
            document.getElementById('tiltdrop-json-output').innerText = JSON.stringify(currentStatus.tiltdrop, null, 2);
            document.getElementById('brakes-json-output').innerText = JSON.stringify(currentStatus.brakes, null, 2);
            document.getElementById('switchtrack-json-output').innerText = JSON.stringify(currentStatus.switchtrack, null, 2);
        }

        function getNested(obj, path) {
            return path.reduce((o, k) => (o && o[k] !== undefined) ? o[k] : undefined, obj);
        }

        function updateMotorUI() {
            const map = [
                { id: 'stationmotor',           esp: 'station',     path: ['motors', 'station', 'stationStepperState'] },
                { id: 'lifthillmotor',           esp: 'station',     path: ['motors', 'lift', 'liftStepperState'] },
                { id: 'stationgatesmotor',       esp: 'station',     path: ['gates', 'gatesMotorState'] },
                { id: 'tiltdropmotor',           esp: 'tiltdrop',    type: 'tiltdrop' },
                { id: 'releasedropmotor',        esp: 'tiltdrop',    path: ['tiltdrop', 'releasedropMotorState'] },
                { id: 'switchtrackmotor',        esp: 'switchtrack', field: 'rotateTarget', type: 'switchtrack' },
                { id: 'releasebrakesmotor',      esp: 'brakes',      path: ['brakes', 'releasebrakesMotorState'] },
                { id: 'releaseswitchtrackmotor', esp: 'switchtrack', path: ['switchtrack', 'releaseswitchMotorState'] },
            ];

            map.forEach(m => {
                const el = document.getElementById(`${m.id}-status`);
                if (!el) return;

                el.classList.remove('text-emerald-400', 'text-red-400', 'text-yellow-400', 'text-orange-400', 'text-gray-600');

                const val = m.path ?
                    getNested(currentStatus[m.esp], m.path) :
                    currentStatus[m.esp]?.[m.field];

                if (m.type === 'switchtrack') {
                    const moving = getNested(currentStatus.switchtrack, ['switchtrack', 'isSwitchtrackMoving']);
                    const target = getNested(currentStatus.switchtrack, ['switchtrack', 'manualRotateTarget']);

                    if (moving) {
                        el.classList.add('text-yellow-400');
                        el.innerText = 'MOVING';
                    } else if (target === 'station') {
                        el.classList.add('text-emerald-400');
                        el.innerText = 'STATION';
                    } else if (target === 'brakes') {
                        el.classList.add('text-emerald-400');
                        el.innerText = 'BRAKES';
                    } else {
                        el.classList.add('text-gray-600');
                        el.innerText = 'UNKNOWN';
                    }
                    return;
                }

                if (m.type === 'tiltdrop') {
                    const moving = getNested(currentStatus.tiltdrop, ['tiltdrop', 'tiltdropMotorMoving']);
                    const open   = getNested(currentStatus.tiltdrop, ['tiltdrop', 'isTiltdropTrackOpen']);
                    const closed = getNested(currentStatus.tiltdrop, ['sensors', 'hallSensorTiltdropClosedState']);

                    if (moving) {
                        el.classList.add('text-red-400');
                        el.innerText = 'MOVING';
                    } else if (open) {
                        el.classList.add('text-orange-400');
                        el.innerText = 'OPEN';
                    } else if (closed) {
                        el.classList.add('text-emerald-400');
                        el.innerText = 'CLOSED';
                    } else {
                        el.classList.add('text-gray-600');
                        el.innerText = 'UNKNOWN';
                    }
                    return;
                }

                if (val === true) {
                    el.classList.add('text-emerald-400');
                    el.innerText = 'ON';
                } else if (val === false) {
                    el.classList.add('text-red-400');
                    el.innerText = 'OFF';
                } else {
                    el.classList.add('text-gray-600');
                    el.innerText = 'LADEN...';
                }
            });
        }

        function updateAllUI() {
            setConnectStatus('station',    currentStatus.stationLwt);
            setConnectStatus('tiltdrop',   currentStatus.tiltdropLwt);
            setConnectStatus('brakes',     currentStatus.brakesLwt);
            setConnectStatus('switchtrack',currentStatus.switchtrackLwt);

            updateMotorUI();
            updateJsonUI();

            ['station', 'tiltdrop', 'brakes', 'switchtrack'].forEach(dev => {
                const toggle = document.getElementById(`manual-switch-${dev}`);
                if (toggle && typeof currentStatus[dev]?.mode?.manualMode !== 'undefined') {
                    toggle.checked = currentStatus[dev].mode.manualMode;
                }
            });
        }

        client.on('connect', () => {
            client.subscribe('rollercoaster/station/status');
            client.subscribe('rollercoaster/tiltdrop/status');
            client.subscribe('rollercoaster/brakes/status');
            client.subscribe('rollercoaster/switchtrack/status');

            client.subscribe('station/status');
            client.subscribe('tiltdrop/status');
            client.subscribe('brakes/status');
            client.subscribe('switchtrack/status');

            resetHeartbeatTimer('station');
            resetHeartbeatTimer('tiltdrop');
            resetHeartbeatTimer('brakes');
            resetHeartbeatTimer('switchtrack');
        });

        client.on('message', (topic, payload) => {
            const msg = payload.toString().trim();

            if (topic === 'rollercoaster/station/status'    && msg === 'online') return resetHeartbeatTimer('station');
            if (topic === 'rollercoaster/tiltdrop/status'   && msg === 'online') return resetHeartbeatTimer('tiltdrop');
            if (topic === 'rollercoaster/brakes/status'     && msg === 'online') return resetHeartbeatTimer('brakes');
            if (topic === 'rollercoaster/switchtrack/status'&& msg === 'online') return resetHeartbeatTimer('switchtrack');

            try {
                const data = JSON.parse(msg);

                if (topic === 'station/status')    currentStatus.station     = data;
                if (topic === 'tiltdrop/status')   currentStatus.tiltdrop    = data;
                if (topic === 'brakes/status')     currentStatus.brakes      = data;
                if (topic === 'switchtrack/status')currentStatus.switchtrack = data;

                updateAllUI();
            } catch {}
        });

        document.getElementById('manual-switch-station')?.addEventListener('change', e =>
            client.publish('station/manual', e.target.checked ? 'on' : 'off')
        );
        document.getElementById('manual-switch-tiltdrop')?.addEventListener('change', e =>
            client.publish('tiltdrop/manual', e.target.checked ? 'on' : 'off')
        );
        document.getElementById('manual-switch-brakes')?.addEventListener('change', e =>
            client.publish('brakes/manual', e.target.checked ? 'on' : 'off')
        );
        document.getElementById('manual-switch-switchtrack')?.addEventListener('change', e =>
            client.publish('switchtrack/manual', e.target.checked ? 'on' : 'off')
        );

        const MOTOR_MAP = {
            stationmotor:           { esp: 'station',    topic: 'station/stationmotor' },
            lifthillmotor:          { esp: 'station',    topic: 'station/lifthillmotor' },
            stationfan:             { esp: 'station',    topic: 'station/stationfan' },
            stationgatesmotor:      { esp: 'station',    topic: 'station/gatesmotor',              type: 'servo' },
            tiltdropmotor:          { esp: 'tiltdrop',   topic: 'tiltdrop/tiltdropmotor',          type: 'servo' },
            releasedropmotor:       { esp: 'tiltdrop',   topic: 'tiltdrop/releasedropmotor',       type: 'servo' },
            releasebrakesmotor:     { esp: 'brakes',     topic: 'brakes/releasebrakesmotor',       type: 'servo' },
            switchtrackmotor:       { esp: 'switchtrack',topic: 'switchtrack/rotatemotor' },
            releaseswitchtrackmotor:{ esp: 'switchtrack',topic: 'switchtrack/releaseswitchtrackmotor', type: 'servo' },
        };

        document.querySelectorAll('.js-esp-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                const action = btn.dataset.action;

                const config = MOTOR_MAP[target];
                if (!config) {
                    console.warn(`No config found for motor: ${target}`);
                    return;
                }

                let finalAction = action;
                if (config.type === 'servo') {
                    finalAction = action === 'on' ? 'open' : 'close';
                }

                client.publish(config.topic, finalAction);
            });
        });

        document.addEventListener('DOMContentLoaded', updateAllUI);
    </script>
</x-layout>
