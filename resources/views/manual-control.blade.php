<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster Manual Controls</h1>

        {{-- Manual Toggles --}}
        <div class="flex flex-row flex-wrap items-center gap-6 mb-6 p-4 bg-white shadow rounded-lg">
            <x-toggle name="manual-switch-station" id="manual-switch-station">Manual Switch Station</x-toggle>
            <x-toggle name="manual-switch-tiltdrop" id="manual-switch-tiltdrop">Manual Switch Tiltdrop</x-toggle>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- LINKERKOLOM: STATUS & JSON DATA --}}
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Informatie & Status</h2>

                {{-- Connectie Status Indicators --}}
                <h3 class="text-xl font-medium mb-2 text-gray-600">Connectie Status (MQTT)</h3>
                <div class="space-y-3 p-4 bg-white shadow rounded-lg">

                    {{-- Station --}}
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Station ESP:</span>
                        <div id="station-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>

                    {{-- Tiltdrop --}}
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Tiltdrop ESP:</span>
                        <div id="tiltdrop-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>

                    {{-- SWITCHTRACK (NIEUW) --}}
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Switchtrack ESP:</span>
                        <div id="switchtrack-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>

                </div>

                {{-- JSON Dumps --}}
                <div class="mb-6">
                    <h3 class="text-xl font-medium mb-2 text-gray-600">JSON Data Dumps (Live)</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded-lg shadow font-mono text-sm overflow-x-auto">

                        <p class="text-gray-400 mb-1">STATION:</p>
                        <pre id="station-json-output">{{ json_encode($station, JSON_PRETTY_PRINT) }}</pre>

                        <p class="text-gray-400 mt-4 mb-1">TILTDROP:</p>
                        <pre id="tiltdrop-json-output">{{ json_encode($tiltdrop, JSON_PRETTY_PRINT) }}</pre>

                        <p class="text-gray-400 mt-4 mb-1">SWITCHTRACK:</p>
                        <pre id="switchtrack-json-output">{{ json_encode($switchtrack ?? [], JSON_PRETTY_PRINT) }}</pre>

                    </div>
                </div>
            </div>


            {{-- RECHTERKOLOM: CONTROLS & MOTOR ACTIES (STATION/TILTDROP ENKEL!) --}}
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Motor Controls</h2>

                <div class="space-y-4">

                    @foreach ([
                        ['id' => 'stationmotor', 'label' => 'STATION MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']],
                        ['id' => 'lifthillmotor', 'label' => 'LIFTHILL MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']],
                        ['id' => 'stationfan', 'label' => 'STATION FAN', 'esp' => 'station', 'actions' => ['on', 'off']],
                        ['id' => 'tiltdropmotor', 'label' => 'TILTDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']],
                        ['id' => 'releasedropmotor', 'label' => 'RELEASEDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']],                         
                    ] as $motor)

                        <x-control-card showStatus>
                            <p class="text-gray-500 text-sm">{{ $motor['label'] }}</p>

                            <x-slot name="buttons">
                                @if ($motor['actions'][0] === 'on')
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}" data-action="on">
                                        {{ strtoupper($motor['actions'][0]) }}
                                    </x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}" data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </x-esp-button>
                                @else
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}" data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}" data-action="on">
                                        {{ strtoupper($motor['actions'][0]) }}
                                    </x-esp-button>
                                @endif
                            </x-slot>

                            <div id="{{ $motor['id'] }}-status"
                                class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                        </x-control-card>

                    @endforeach
                </div>

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
            switchtrack: {},
            stationLwt: '{{ $stationOnline }}',
            tiltdropLwt: '{{ $tiltdropOnline }}',
            switchtrackLwt: '{{ $switchtrackOnline ?? "offline" }}'
        };

        const HEARTBEAT_TIMEOUT_MS = 4500;
        const HEARTBEAT_TIMERS = { station: null, tiltdrop: null, switchtrack: null };

        function setConnectStatus(device, status) {
            currentStatus[`${device}Lwt`] = status;
            const el = document.getElementById(`${device}-connect-status`);
            if (!el) return;

            el.classList.remove('bg-green-500','bg-red-500','bg-gray-400');
            if (status === 'online') { el.classList.add('bg-green-500'); el.innerText = 'Online'; }
            else if (status === 'offline') { el.classList.add('bg-red-500'); el.innerText = 'Offline'; }
            else { el.classList.add('bg-gray-400'); el.innerText = 'Laden...'; }
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
            document.getElementById('switchtrack-json-output').innerText = JSON.stringify(currentStatus.switchtrack, null, 2);
        }

        function updateMotorUI() {
            const map = [
                { id: 'stationmotor', esp: 'station', field: 'stationMotorState' },
                { id: 'lifthillmotor', esp: 'station', field: 'liftMotorState' },
                { id: 'tiltdropmotor', esp: 'tiltdrop', field: 'isTiltdropTrackOpen' },
                { id: 'releasedropmotor', esp: 'tiltdrop', field: 'releasedropMotorState' },
                { id: 'stationfan', esp: 'station', field: 'relayState' },
            ];

            map.forEach(m => {
                const el = document.getElementById(`${m.id}-status`);
                if (!el) return;

                el.classList.remove('bg-green-500','bg-red-500','bg-yellow-500','bg-gray-400');
                const val = currentStatus[m.esp]?.[m.field];

                if (val === true) { el.classList.add('bg-green-500'); el.innerText = 'ON'; }
                else if (val === false) { el.classList.add('bg-red-500'); el.innerText = 'OFF'; }
                else { el.classList.add('bg-gray-400'); el.innerText = 'Laden...'; }
            });
        }

        function updateAllUI() {
            setConnectStatus('station', currentStatus.stationLwt);
            setConnectStatus('tiltdrop', currentStatus.tiltdropLwt);
            setConnectStatus('switchtrack', currentStatus.switchtrackLwt);

            updateMotorUI();
            updateJsonUI();

            ['station','tiltdrop'].forEach(dev => {
                const toggle = document.getElementById(`manual-switch-${dev}`);
                if (toggle && typeof currentStatus[dev]?.manualMode !== 'undefined') {
                    toggle.checked = currentStatus[dev].manualMode;
                }
            });
        }

        client.on('connect', () => {
            client.subscribe('rollercoaster/station/status');
            client.subscribe('rollercoaster/tiltdrop/status');
            client.subscribe('rollercoaster/switchtrack/status');

            client.subscribe('station/status');
            client.subscribe('tiltdrop/status');
            client.subscribe('switchtrack/status');

            resetHeartbeatTimer('station');
            resetHeartbeatTimer('tiltdrop');
            resetHeartbeatTimer('switchtrack');
        });

        client.on('message', (topic, payload) => {
            const msg = payload.toString().trim();

            if (topic === 'rollercoaster/station/status' && msg === 'online') return resetHeartbeatTimer('station');
            if (topic === 'rollercoaster/tiltdrop/status' && msg === 'online') return resetHeartbeatTimer('tiltdrop');
            if (topic === 'rollercoaster/switchtrack/status' && msg === 'online') return resetHeartbeatTimer('switchtrack');

            try {
                const data = JSON.parse(msg);

                if (topic === 'station/status') currentStatus.station = data;
                if (topic === 'tiltdrop/status') currentStatus.tiltdrop = data;
                if (topic === 'switchtrack/status') currentStatus.switchtrack = data;

                updateAllUI();
            } catch {}
        });

        document.getElementById('manual-switch-station')?.addEventListener('change', e =>
            client.publish('station/manual', e.target.checked ? 'on' : 'off')
        );
        document.getElementById('manual-switch-tiltdrop')?.addEventListener('change', e =>
            client.publish('tiltdrop/manual', e.target.checked ? 'on' : 'off')
        );

        document.querySelectorAll('.js-esp-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                let action = btn.dataset.action;

                const tiltdropMotors = ['tiltdropmotor','releasedropmotor'];
                if (tiltdropMotors.includes(target)) {
                    action = action === 'on' ? 'open' : 'close';
                }

                let topic = ['stationmotor','lifthillmotor','stationfan'].includes(target)
                    ? `station/${target}`
                    : `tiltdrop/${target}`;

                client.publish(topic, action);
            });
        });

        document.addEventListener('DOMContentLoaded', updateAllUI);
    </script>
</x-layout>
