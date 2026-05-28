<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster Manual Controls</h1>

        {{-- Manual Toggles --}}
        <div class="flex flex-row flex-wrap items-center gap-6 mb-6 p-4 bg-white shadow rounded-lg font-semibold">
            <x-toggle name="manual-switch-station" id="manual-switch-station">STATION</x-toggle>
            <x-toggle name="manual-switch-tiltdrop" id="manual-switch-tiltdrop">TILTDROP</x-toggle>
            <x-toggle name="manual-switch-brakes" id="manual-switch-brakes">BRAKES</x-toggle>
            <x-toggle name="manual-switch-switchtrack" id="manual-switch-switchtrack">SWITCHTRACK</x-toggle>
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

                    {{-- BRAKES --}}
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Brakes ESP:</span>
                        <div id="brakes-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>

                    {{-- SWITCHTRACK --}}
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Switchtrack ESP:</span>
                        <div id="switchtrack-connect-status"
                            class="p-2 w-24 text-center rounded text-white bg-gray-400">
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

                        <p class="text-gray-400 mt-4 mb-1">BRAKES:</p>
                        <pre id="brakes-json-output">{{ json_encode($brakes, JSON_PRETTY_PRINT) }}</pre>

                        <p class="text-gray-400 mt-4 mb-1">SWITCHTRACK:</p>
                        <pre id="switchtrack-json-output">{{ json_encode($switchtrack ?? [], JSON_PRETTY_PRINT) }}</pre>

                    </div>
                </div>
            </div>


            {{-- RECHTERKOLOM: CONTROLS & MOTOR ACTIES (STATION/TILTDROP ENKEL!) --}}
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Motor Controls</h2>

                <div class="space-y-4">

                    @foreach ([['id' => 'stationmotor', 'label' => 'STATION MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']], ['id' => 'lifthillmotor', 'label' => 'LIFTHILL MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']], ['id' => 'gatesmotor', 'label' => 'GATES MOTOR', 'esp' => 'station', 'actions' => ['open', 'close']], ['id' => 'tiltdropmotor', 'label' => 'TILTDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']], ['id' => 'releasedropmotor', 'label' => 'RELEASEDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']], ['id' => 'misteffect', 'label' => 'MIST EFFECT', 'esp' => 'tiltdrop', 'actions' => ['on', 'off']], ['id' => 'releasebrakesmotor', 'label' => 'RELEASE BRAKES MOTOR', 'esp' => 'brakes', 'actions' => ['open', 'close']], ['id' => 'switchtrackmotor', 'label' => 'SWITCHTRACK MOTOR', 'esp' => 'switchtrack', 'actions' => ['brakes', 'station']], ['id' => 'releaseswitchtrackmotor', 'label' => 'RELEASE SWITHCTRACK MOTOR', 'esp' => 'switchtrack', 'actions' => ['open', 'close']]] as $motor)
                        <x-control-card showStatus>
                            <p class="text-gray-500 text-sm">{{ $motor['label'] }}</p>

                            <x-slot name="buttons">
                                @if ($motor['actions'][0] === 'on')
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="on">
                                        {{ strtoupper($motor['actions'][0]) }}
                                    </x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </x-esp-button>
                                @else
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="off">
                                        {{ strtoupper($motor['actions'][1]) }}
                                    </x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="on">
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

            el.classList.remove('bg-green-500', 'bg-red-500', 'bg-gray-400');
            if (status === 'online') {
                el.classList.add('bg-green-500');
                el.innerText = 'Online';
            } else if (status === 'offline') {
                el.classList.add('bg-red-500');
                el.innerText = 'Offline';
            } else {
                el.classList.add('bg-gray-400');
                el.innerText = 'Laden...';
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
            document.getElementById('switchtrack-json-output').innerText = JSON.stringify(currentStatus.switchtrack, null,
                2);
        }

        function getNested(obj, path) {
            return path.reduce((o, k) => (o && o[k] !== undefined) ? o[k] : undefined, obj);
        }


        function updateMotorUI() {
            const map = [{
                    id: 'stationmotor',
                    esp: 'station',
                    path: ['motors', 'station', 'stationStepperState']
                },
                {
                    id: 'lifthillmotor',
                    esp: 'station',
                    path: ['motors', 'lift', 'liftStepperState']
                },
                {
                    id: 'gatesmotor',
                    esp: 'station',
                    path: ['motors', 'station', 'gatesServoState']
                },
                {
                    id: 'tiltdropmotor',
                    esp: 'tiltdrop',
                    type: 'tiltdrop'
                },
                {
                    id: 'releasedropmotor',
                    esp: 'tiltdrop',
                    path: ['tiltdrop', 'releasedropMotorState']
                },
                {
                    id: 'misteffect',
                    esp: 'tiltdrop',
                    path: ['tiltdrop', 'misteffectState']
                },
                {
                    id: 'releasebrakesmotor',
                    esp: 'brakes',
                    path: ['motors', 'releaseBrakesMotorState']
                },
                {
                    id: 'switchtrackmotor',
                    esp: 'switchtrack',
                    field: 'rotateTarget',
                    type: 'switchtrack'
                },

                {
                    id: 'releaseswitchtrackmotor',
                    esp: 'switchtrack',
                    path: ['switchtrack', 'releaseswitchMotorState']
                },
            ];

            map.forEach(m => {
                const el = document.getElementById(`${m.id}-status`);
                if (!el) return;

                el.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'bg-gray-400');

                const val = m.path ?
                    getNested(currentStatus[m.esp], m.path) :
                    currentStatus[m.esp]?.[m.field];


                // Speciale UI handling voor switchtrack rotateTarget
                if (m.type === 'switchtrack') {
                    const moving = getNested(currentStatus.switchtrack, ['switchtrack', 'isSwitchtrackMoving']);
                    const target = getNested(currentStatus.switchtrack, ['switchtrack', 'manualRotateTarget']);

                    if (moving) {
                        el.classList.add('bg-yellow-500');
                        el.innerText = 'MOVING';
                    } else if (target === 'station') {
                        el.classList.add('bg-green-500');
                        el.innerText = 'STATION';
                    } else if (target === 'brakes') {
                        el.classList.add('bg-green-500');
                        el.innerText = 'BRAKES';
                    } else {
                        el.classList.add('bg-gray-400');
                        el.innerText = 'UNKNOWN';
                    }
                    return;
                }

                if (m.type === 'tiltdrop') {
                    const moving = getNested(currentStatus.tiltdrop, ['tiltdrop', 'tiltdropMotorMoving']);
                    const open = getNested(currentStatus.tiltdrop, ['tiltdrop', 'isTiltdropTrackOpen']);
                    const closed = getNested(currentStatus.tiltdrop, ['sensors', 'hallSensorTiltdropClosedState']);

                    if (moving) {
                        el.classList.add('bg-red-500');
                        el.innerText = 'MOVING';
                    } else if (open) {
                        el.classList.add('bg-orange-500');
                        el.innerText = 'OPEN';
                    } else if (closed) {
                        el.classList.add('bg-green-500');
                        el.innerText = 'CLOSED';
                    } else {
                        el.classList.add('bg-gray-400');
                        el.innerText = 'UNKNOWN';
                    }
                    return;
                }

                if (val === true) {
                    el.classList.add('bg-green-500');
                    el.innerText = 'ON';
                } else if (val === false) {
                    el.classList.add('bg-red-500');
                    el.innerText = 'OFF';
                } else {
                    el.classList.add('bg-gray-400');
                    el.innerText = 'Laden...';
                }
            });

        }

        function updateAllUI() {
            setConnectStatus('station', currentStatus.stationLwt);
            setConnectStatus('tiltdrop', currentStatus.tiltdropLwt);
            setConnectStatus('brakes', currentStatus.brakesLwt);
            setConnectStatus('switchtrack', currentStatus.switchtrackLwt);

            updateMotorUI();
            updateJsonUI();

            ['station', 'tiltdrop', 'brakes', 'switchtrack'].forEach(dev => {
                const toggle = document.getElementById(`manual-switch-${dev}`);
                if (toggle && typeof currentStatus[dev]?.mode.manualMode !== 'undefined') {
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

            if (topic === 'rollercoaster/station/status' && msg === 'online') return resetHeartbeatTimer('station');
            if (topic === 'rollercoaster/tiltdrop/status' && msg === 'online') return resetHeartbeatTimer(
                'tiltdrop');
                if (topic === 'rollercoaster/brakes/status' && msg === 'online') return resetHeartbeatTimer(
                'brakes');
            if (topic === 'rollercoaster/switchtrack/status' && msg === 'online') return resetHeartbeatTimer(
                'switchtrack');

            try {
                const data = JSON.parse(msg);

                if (topic === 'station/status') currentStatus.station = data;
                if (topic === 'tiltdrop/status') currentStatus.tiltdrop = data;
                if (topic === 'brakes/status') currentStatus.brakes = data;
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
        document.getElementById('manual-switch-brakes')?.addEventListener('change', e =>
            client.publish('brakes/manual', e.target.checked ? 'on' : 'off')
        );
        document.getElementById('manual-switch-switchtrack')?.addEventListener('change', e =>
            client.publish('switchtrack/manual', e.target.checked ? 'on' : 'off')
        );

        const MOTOR_MAP = {
            stationmotor: {
                esp: "station",
                topic: "station/stationmotor"
            },
            lifthillmotor: {
                esp: "station",
                topic: "station/lifthillmotor"
            },
            gatesmotor: {
                esp: "station",
                topic: "station/gatesmotor",
                type: "servo"
            },
            stationfan: {
                esp: "station",
                topic: "station/stationfan"
            },

            tiltdropmotor: {
                esp: "tiltdrop",
                topic: "tiltdrop/tiltdropmotor",
                type: "servo"
            },
            misteffect: {
                esp: "tiltdrop",
                topic: "tiltdrop/misteffect"
            },

            releasedropmotor: {
                esp: "tiltdrop",
                topic: "tiltdrop/releasedropmotor",
                type: "servo"
            },
            releasebrakesmotor: {
                esp: "brakes",
                topic: "brakes/releasebrakesmotor",
                type: "servo"
            },

            switchtrackmotor: {
                esp: "switchtrack",
                topic: "switchtrack/rotatemotor"
            },
            releaseswitchtrackmotor: {
                esp: "switchtrack",
                topic: "switchtrack/releaseswitchtrackmotor",
                type: "servo"
            },
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

                // Speciale gevallen (servo’s, open/close mapping)
                if (config.type === "servo") {
                    finalAction = action === 'on' ? 'open' : 'close';
                }

                client.publish(config.topic, finalAction);
            });
        });


        document.addEventListener('DOMContentLoaded', updateAllUI);
    </script>
</x-layout>
