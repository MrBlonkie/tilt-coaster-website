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
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Station ESP:</span>
                        <div id="station-connect-status"
                            class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Tiltdrop ESP:</span>
                        <div id="tiltdrop-connect-status"
                            class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...</div>
                    </div>
                </div>

                {{-- JSON Dumps --}}
                <div class="mb-6">
                    <h3 class="text-xl font-medium mb-2 text-gray-600">JSON Data Dumps (Live)</h3>
                    <div
                        class="bg-gray-800 text-green-400 p-4 rounded-lg shadow font-mono text-sm overflow-x-auto">
                        <p class="text-gray-400 mb-1">STATION:</p>
                        <pre id="station-json-output">{{ json_encode($station, JSON_PRETTY_PRINT) }}</pre>
                        <p class="text-gray-400 mt-4 mb-1">TILTDROP:</p>
                        <pre id="tiltdrop-json-output">{{ json_encode($tiltdrop, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>

            {{-- RECHTERKOLOM: CONTROLS & MOTOR ACTIES --}}
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Motor Controls</h2>
                <div class="space-y-4">
                    @foreach ([['id' => 'stationmotor', 'label' => 'STATION MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']], ['id' => 'lifthillmotor', 'label' => 'LIFTHILL MOTOR', 'esp' => 'station', 'actions' => ['on', 'off']], ['id' => 'tiltdropmotor', 'label' => 'TILTDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']], ['id' => 'releasedropmotor', 'label' => 'RELEASEDROP MOTOR', 'esp' => 'tiltdrop', 'actions' => ['open', 'close']]] as $motor)
                        <x-control-card showStatus>
                            <p class="text-gray-500 text-sm">{{ $motor['label'] }}</p>
                            <x-slot name="buttons">
                                {{-- START AANPASSING --}}
                                @if ($motor['actions'][0] === 'on')
                                    {{-- Volgorde voor ON/OFF: ON (links), OFF (rechts) --}}
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="on">{{ strtoupper($motor['actions'][0]) }}</x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="off">{{ strtoupper($motor['actions'][1]) }}</x-esp-button>
                                @else
                                    {{-- Volgorde voor OPEN/CLOSE: CLOSE (links), OPEN (rechts) --}}
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="off">{{ strtoupper($motor['actions'][1]) }}</x-esp-button>
                                    <x-esp-button class="js-esp-button" data-target="{{ $motor['id'] }}"
                                        data-action="on">{{ strtoupper($motor['actions'][0]) }}</x-esp-button>
                                @endif
                                {{-- EINDE AANPASSING --}}
                            </x-slot>
                            <div id="{{ $motor['id'] }}-status"
                                class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                        </x-control-card>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const client = mqtt.connect('ws://10.11.171.126:9001');
        const currentStatus = {
            station: {},
            tiltdrop: {},
            // We gebruiken deze properties om de status bij te houden, 
            // maar de initiële waarden uit de backend zijn 'unknown'.
            stationLwt: '{{ $stationOnline }}',
            tiltdropLwt: '{{ $tiltdropOnline }}'
        };

        // === HEARTBEAT LOGICA START ===
        const HEARTBEAT_TIMEOUT_MS = 4500; // 4.5 seconden time-out (non-blocking)
        const HEARTBEAT_TIMERS = {
            station: null,
            tiltdrop: null
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
            // Wis de vorige timer
            if (HEARTBEAT_TIMERS[device]) {
                clearTimeout(HEARTBEAT_TIMERS[device]);
            }

            // Zet status meteen op 'online'
            setConnectStatus(device, 'online');

            // Start een nieuwe timer die de status naar 'offline' zet als hij afloopt (non-blocking)
            HEARTBEAT_TIMERS[device] = setTimeout(() => {
                console.warn(`Heartbeat timeout for ${device}. Setting status to offline.`);
                setConnectStatus(device, 'offline');
            }, HEARTBEAT_TIMEOUT_MS);
        }

        // De oorspronkelijke updateLwtUI wordt vervangen door de logica in setConnectStatus
        function updateLwtUI() {
            setConnectStatus('station', currentStatus.stationLwt);
            setConnectStatus('tiltdrop', currentStatus.tiltdropLwt);
        }
        // === HEARTBEAT LOGICA EIND ===


        function updateMotorUI() {
            const map = [{
                    id: 'stationmotor',
                    esp: 'station',
                    field: 'stationMotorState'
                },
                {
                    id: 'lifthillmotor',
                    esp: 'station',
                    field: 'liftMotorState'
                },
                // Veld is nu irrelevant voor status, de status wordt bepaald door de 3 booleans
                {
                    id: 'tiltdropmotor',
                    esp: 'tiltdrop',
                    field: 'isTiltdropTrackOpen' 
                }, 
                {
                    id: 'releasedropmotor',
                    esp: 'tiltdrop',
                    field: 'releasedropMotorState'
                }
            ];
            map.forEach(m => {
                const el = document.getElementById(`${m.id}-status`);
                if (!el) return;
                
                // === NIEUWE LOGICA VOOR TILTDROPMOTOR ===
                if (m.id === 'tiltdropmotor') {
                    const isMoving = currentStatus.tiltdrop?.tiltdropMotorMoving ?? null;
                    const isOpen = currentStatus.tiltdrop?.hallSensorTiltdropOpen ?? null;
                    const isClosed = currentStatus.tiltdrop?.hallSensorTiltdropClosed ?? null; // Gebruik de hallSensor voor Closed status
                    
                    el.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'bg-gray-400');
                    
                    if (isMoving === true) {
                        el.classList.add('bg-yellow-500');
                        el.innerText = 'MOVING';
                    } else if (isOpen === true) {
                        el.classList.add('bg-green-500');
                        el.innerText = 'OPEN';
                    } else if (isClosed === true) {
                        el.classList.add('bg-red-500'); // Rood is hier logisch voor de 'veilige/gesloten' positie
                        el.innerText = 'CLOSED';
                    } else {
                        el.classList.add('bg-gray-400');
                        el.innerText = 'Laden...';
                    }
                    return; // Stop de loop voor tiltdropmotor, want deze is afgehandeld
                } 
                // === EINDE NIEUWE LOGICA ===


                // Oude logica voor andere motoren (stationmotor, lifthillmotor, releasedropmotor)
                const val = currentStatus[m.esp]?.[m.field] ?? null;
                el.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'bg-gray-400');

                let text = 'Laden...';
                if (val === true) {
                    el.classList.add('bg-green-500');
                    text = (m.id === 'releasedropmotor') ? 'OPEN' : 'ON';
                } else if (val === false) {
                    el.classList.add('bg-red-500');
                    text = (m.id === 'releasedropmotor') ? 'CLOSED' : 'OFF';
                } else {
                    el.classList.add('bg-gray-400');
                }
                el.innerText = text;
            });
        }

        function updateJsonUI() {
            document.getElementById('station-json-output').innerText = JSON.stringify(currentStatus.station, null, 2);
            document.getElementById('tiltdrop-json-output').innerText = JSON.stringify(currentStatus.tiltdrop, null, 2);
        }

        function updateAllUI() {
            updateLwtUI(); // Toon de initiële status
            updateMotorUI();
            updateJsonUI();
            ['station', 'tiltdrop'].forEach(dev => {
                const toggle = document.getElementById(`manual-switch-${dev}`);
                if (toggle && typeof currentStatus[dev]?.manualMode !== 'undefined') toggle.checked = currentStatus[
                    dev].manualMode;
            });
        }

        client.on('connect', () => {
            console.log('MQTT connected!');
            // ABONNEER op de Heartbeat topics
            client.subscribe('rollercoaster/station/status');
            client.subscribe('rollercoaster/tiltdrop/status');
            // ABONNEER op de gedetailleerde status topics
            client.subscribe('station/status');
            client.subscribe('tiltdrop/status');

            // Start de Heartbeat timers onmiddellijk om te detecteren of de ESPs al down zijn
            resetHeartbeatTimer('station');
            resetHeartbeatTimer('tiltdrop');
        });

        client.on('message', (topic, payload) => {
            const msg = payload.toString().trim();

            // === HEARTBEAT BERICHTEN AFHANDELEN ===
            if (topic === 'rollercoaster/station/status') {
                if (msg.toLowerCase() === 'online') {
                    resetHeartbeatTimer('station'); // Reset de timer
                }
                return;
            }
            if (topic === 'rollercoaster/tiltdrop/status') {
                if (msg.toLowerCase() === 'online') {
                    resetHeartbeatTimer('tiltdrop'); // Reset de timer
                }
                return;
            }
            // === EINDE HEARTBEAT AFHANDELING ===

            try {
                const data = JSON.parse(msg);
                if (topic === 'station/status') currentStatus.station = data;
                if (topic === 'tiltdrop/status') currentStatus.tiltdrop = data;
                updateAllUI();
            } catch (e) {
                console.error('Invalid JSON', e);
            }
        });

        document.getElementById('manual-switch-station')?.addEventListener('change', e => {
            client.publish('station/manual', e.target.checked ? 'on' : 'off');
        });
        document.getElementById('manual-switch-tiltdrop')?.addEventListener('change', e => {
            client.publish('tiltdrop/manual', e.target.checked ? 'on' : 'off');
        });

        // *** AANGEPASTE LOGICA VOOR MOTOR BEDIENING ***
        document.querySelectorAll('.js-esp-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                let action = btn.dataset.action; // Standaard: 'on' of 'off'

                // Controleer of het een Tiltdrop motor is die 'open'/'close' moet gebruiken
                const isTiltdropMotor = ['tiltdropmotor', 'releasedropmotor'].includes(target);

                if (isTiltdropMotor) {
                    // Vertaal de 'on'/'off' data-action naar 'open'/'close' MQTT-bericht
                    if (action === 'on') {
                        action = 'open';
                    } else if (action === 'off') {
                        action = 'close';
                    }
                }

                // Bepaal het topic: station/... of tiltdrop/...
                let topic = ['stationmotor', 'lifthillmotor'].includes(target) ? `station/${target}` :
                    `tiltdrop/${target}`;

                console.log(`Publishing to topic: ${topic} with action: ${action}`);
                client.publish(topic, action);
            });
        });

        document.addEventListener('DOMContentLoaded', updateAllUI);
    </script>
</x-layout>