<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster Control Tower</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM 1: VISUELE BAAN (MIMIC PANEL) --}}
            <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Mimic Panel</h2>

                <svg id="mimic-panel" class="w-full" viewBox="0 0 600 250" preserveAspectRatio="xMidYMid meet">
                    <style>
                        .track-segment {
                            stroke: #cbd5e1;
                            stroke-width: 10;
                            fill: none;
                            transition: stroke 0.3s;
                        }

                        .track-segment.active {
                            stroke: #3b82f6;
                            stroke-width: 12;
                        }

                        .sensor {
                            fill: #9ca3af;
                            stroke: #4b5563;
                            stroke-width: 1;
                            r: 8;
                            transition: fill 0.3s;
                        }

                        .sensor.active {
                            fill: #22c55e;
                            r: 10;
                        }

                        .sensor-label {
                            font-size: 10px;
                            font-family: sans-serif;
                            fill: #374151;
                            text-anchor: middle;
                        }
                    </style>

                    <defs>
                        <g id="sensor-group">
                            <circle class="sensor" cx="0" cy="0" />
                            <text class="sensor-label" x="0" y="-15"></text>
                        </g>
                    </defs>

                    {{-- Station --}}
                    <rect x="20" y="50" width="250" height="50" rx="5" fill="#f3f4f6" stroke="#e5e7eb"
                        stroke-width="2" />
                    <text x="145" y="80" text-anchor="middle" font-weight="bold" fill="#4b5563">STATION</text>
                    <path id="block-station" class="track-segment" d="M 40 100 H 260" />

                    <use href="#sensor-group" x="60" y="100" id="sensor-enterStation" data-label="Enter" />
                    <use href="#sensor-group" x="150" y="100" id="sensor-startPosition" data-label="Start" />
                    <use href="#sensor-group" x="240" y="100" id="sensor-exitStation" data-label="Exit" />

                    {{-- Lifthill --}}
                    <path id="block-lifthill" class="track-segment" d="M 260 100 L 320 40 L 380 40" />
                    <use href="#sensor-group" x="270" y="90" id="sensor-bottomLifthill" data-label="Lift Bottom" />
                    <use href="#sensor-group" x="370" y="40" id="sensor-topLifthill" data-label="Lift Top" />

                    {{-- Tiltdrop Section --}}
                    <path id="block-tiltdrop" class="track-segment" d="M 380 40 L 450 40" />
                    <rect id="tiltdrop-element" x="450" y="35" width="80" height="10" rx="2"
                        fill="#e2e8f0" transition="fill 0.3s" />
                    <text x="490" y="25" class="sensor-label">Tiltdrop</text>

                    <use href="#sensor-group" x="460" y="60" id="sensor-tiltdropClosed" data-label="Tilt Closed" />
                    <use href="#sensor-group" x="490" y="60" id="sensor-tiltdropOpen" data-label="Tilt Open" />
                    <use href="#sensor-group" x="520" y="60" id="sensor-coasterOnTiltdrop" data-label="Coaster On" />

                    {{-- Return Track --}}
                    <path id="block-return" class="track-segment"
                        d="M 530 40 C 600 40, 600 200, 400 200 L 40 200 L 40 100" />

                </svg>
            </div>

            {{-- KOLOM 2: STATUS & CONTROLS --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Status Card --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">System Status</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Modus:</span>
                            <span id="status-mode" class="font-bold text-lg text-gray-800">Laden...</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Systeem Status:</span>
                            <span id="status-state" class="font-bold text-lg text-blue-600">Laden...</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Coaster:</span>
                            <span id="status-coaster" class="font-bold text-lg text-gray-800">Laden...</span>
                        </div>
                    </div>
                </div>

                {{-- Connection Card --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Connecties</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Station ESP:</span>
                            <div id="station-connect-status"
                                class="p-2 w-24 text-center rounded text-white bg-gray-400">Laden...</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Tiltdrop ESP:</span>
                            <div id="tiltdrop-connect-status"
                                class="p-2 w-24 text-center rounded text-white bg-gray-400">Laden...</div>
                        </div>
                    </div>
                </div>

                {{-- Controls Card --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Controls</h2>
                    <div class="space-y-4">
                        <button id="dispatch-button"
                            class="w-full text-white font-bold py-3 px-4 rounded transition duration-300 ease-in-out bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed">DISPATCH</button>
                        <button id="estop-button"
                            class="w-full text-white font-bold py-3 px-4 rounded transition duration-300 ease-in-out bg-red-600 hover:bg-red-700">EMERGENCY
                            STOP</button>
                    </div>
                </div>

            </div>

        </div>
        <div class="bg-black text-green-400 font-mono rounded-lg p-4 mt-6 h-64 overflow-y-auto" id="event-log">
            <h2 class="text-xl font-semibold mb-2 text-white">Event Log</h2>
            <div id="log-messages" class="space-y-1 text-sm"></div>
        </div>

    </div>

    <script>
        // === Heartbeat tracking ===
        let lastStationHeartbeat = 0;
        let lastTiltdropHeartbeat = 0;
        const HEARTBEAT_TIMEOUT = 5000; // 5 seconden

        // === MQTT CLIENT SETUP ===
        const MQTT_HOST = "{{ env('PI_IP') }}";
        const client = mqtt.connect(`ws://${MQTT_HOST}:9001`);

        client.on('connect', () => {
            console.log('MQTT connected!');
            client.subscribe([
                'rollercoaster/station/status',
                'rollercoaster/tiltdrop/status',
                'rollercoaster/event',
                'station/status',
                'tiltdrop/status'
            ]);

            // Initialiseer connectie status
            setConnectStatus('station', 'unknown');
            setConnectStatus('tiltdrop', 'unknown');
        });

        client.publish('station/manual', 'off');
        client.publish('tiltdrop/manual', 'off');

        client.on('message', (topic, payload) => {
            const msg = payload.toString().trim();

            // === LWT / HEARTBEAT ===
            if (topic === 'rollercoaster/station/status') {
                if (msg.toLowerCase() === 'online') lastStationHeartbeat = Date.now();
                setConnectStatus('station', 'online');
                return;
            }
            if (topic === 'rollercoaster/tiltdrop/status') {
                if (msg.toLowerCase() === 'online') lastTiltdropHeartbeat = Date.now();
                setConnectStatus('tiltdrop', 'online');
                return;
            }

            if (topic === 'rollercoaster/event') {
                appendLogMessage(msg);
                return;
            }

            // === STATUS BERICHT (JSON only) ===
            let data;
            try {
                data = JSON.parse(msg);
            } catch (e) {
                // Niet-JSON (bijv. heartbeat strings) negeren
                return;
            }

            if (topic === 'station/status') updateStationUI(data);
            if (topic === 'tiltdrop/status') updateTiltdropUI(data);
        });

        function updateStationUI(data) {
            if (!data) return;

            document.getElementById('status-mode').textContent = data.manualMode ? "Manual" : "Auto";
            document.getElementById('status-state').textContent = data.currentState || "Unknown";

            const coasterStatusEl = document.getElementById('status-coaster');
            if (data.coasterDispatched) {
                coasterStatusEl.textContent = "Dispatched";
                coasterStatusEl.className = "font-bold text-lg text-green-600";
            } else {
                coasterStatusEl.textContent = "In Station";
                coasterStatusEl.className = "font-bold text-lg text-gray-800";
            }

            const dispatchBtn = document.getElementById('dispatch-button');
            const isReady = data.currentState === 'IDLE' && !data.manualMode;
            dispatchBtn.disabled = !isReady;
            dispatchBtn.textContent = isReady ? "DISPATCH" : `NIET KLAAR (${data.currentState})`;

            updateSensor('sensor-enterStation', !!data.hallSensorEnterStation);
            updateSensor('sensor-startPosition', !!data.hallSensorStartPosition);
            updateSensor('sensor-exitStation', !!data.hallSensorExitStation);
            updateSensor('sensor-bottomLifthill', !!data.hallSensorBottomLifthill);
            updateSensor('sensor-topLifthill', !!data.hallSensorTopLifthill);
        }

        function updateTiltdropUI(data) {
            if (!data) return;

            updateSensor('sensor-tiltdropClosed', !!data.hallSensorTiltdropClosed);
            updateSensor('sensor-tiltdropOpen', !!data.hallSensorTiltdropOpen);
            updateSensor('sensor-coasterOnTiltdrop', !!data.hallSensorOnTiltdrop);

            const tiltEl = document.getElementById('tiltdrop-element');
            if (data.hallSensorTiltdropOpen) tiltEl.style.fill = '#ef4444';
            else if (data.hallSensorTiltdropClosed) tiltEl.style.fill = '#22c55e';
            else tiltEl.style.fill = '#ff8c00ff';
        }


        // === Heartbeat interval ===
        setInterval(() => {
            if (Date.now() - lastStationHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('station', 'offline');
            if (Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('tiltdrop', 'offline');
        }, 1000);



        function updateSensor(id, isActive) {
            const el = document.getElementById(id);
            if (!el) return;

            // toggle class op <use> zelf ipv .sensor
            if (isActive) el.classList.add('active');
            else el.classList.remove('active');
        }


        function setConnectStatus(device, status) {
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

        // === CONTROLS ===
        document.getElementById('dispatch-button').addEventListener('click', () => {
            console.log('Publishing: rollercoaster/dispatch -> go');
            client.publish('rollercoaster/dispatch', 'go');

            const dispatchBtn = document.getElementById('dispatch-button');
            dispatchBtn.disabled = true;
            dispatchBtn.textContent = 'DISPATCHING...';
        });

        document.getElementById('estop-button').addEventListener('click', () => {
            console.log('Publishing: rollercoaster/estop -> true');
            client.publish('rollercoaster/estop', 'true');
        });

        function appendLogMessage(message) {
            const logContainer = document.getElementById('log-messages');
            const timestamp = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.textContent = `[${timestamp}] ${message}`;
            logContainer.appendChild(entry);
            logContainer.parentElement.scrollTop = logContainer.parentElement.scrollHeight;

            if (logContainer.children.length > 200) logContainer.removeChild(logContainer.firstChild);
        }
    </script>
</x-layout>
