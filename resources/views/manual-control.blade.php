<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster manual controls</h1>

        {{-- Manual Toggles (Nu bovenaan, over de volledige breedte) --}}
        {{-- 1. Aangepast van space-y-4 naar flex en gap voor horizontale weergave --}}
        <div class="flex flex-row flex-wrap items-center gap-6 mb-6 p-4 bg-white shadow rounded-lg">
            <x-toggle name="manual-switch-station" id="manual-switch-station">**Manual Switch Station**</x-toggle>
            <x-toggle name="manual-switch-tiltdrop" id="manual-switch-tiltdrop">**Manual Switch Tiltdrop**</x-toggle>
        </div>
        
        {{-- 2. De grid start nu HIER, na de toggles --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- LINKERKOLOM: STATUS & JSON DATA --}}
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Informatie & Status</h2>
                
                {{-- De toggles stonden hier, maar zijn naar boven verplaatst --}}

                {{-- JSON Dumps: Volledige Status Data --}}
                <div class="mb-6">
                    <h3 class="text-xl font-medium mb-2 text-gray-600">JSON Data Dumps (Live)</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded-lg shadow font-mono text-sm overflow-x-auto">
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
                    {{-- STATION MOTOR --}}
                    <x-control-card showStatus>
                        <p class="text-gray-500 text-sm">STATION MOTOR</p>
                        <x-slot name="buttons">
                            <x-esp-button class="js-esp-button" data-target="stationmotor" data-action="on">ON</x-esp-button>
                            <x-esp-button class="js-esp-button" data-target="stationmotor" data-action="off">OFF</x-esp-button>
                        </x-slot>
                        <div id="stationmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                    </x-control-card>

                    {{-- LIFTHILL MOTOR --}}
                    <x-control-card showStatus>
                        <p class="text-gray-500 text-sm">LIFTHILL MOTOR</p>
                        <x-slot name="buttons">
                            <x-esp-button class="js-esp-button" data-target="lifthillmotor" data-action="on">ON</x-esp-button>
                            <x-esp-button class="js-esp-button" data-target="lifthillmotor" data-action="off">OFF</x-esp-button>
                        </x-slot>
                        <div id="lifthillmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                    </x-control-card>

                    {{-- TILTDROP MOTOR --}}
                    <x-control-card showStatus>
                        <p class="text-gray-500 text-sm">TILTDROP MOTOR</p>
                        <x-slot name="buttons">
                            <x-esp-button class="js-esp-button" data-target="tiltdropmotor" data-action="open">OPEN</x-esp-button>
                            <x-esp-button class="js-esp-button" data-target="tiltdropmotor" data-action="close">CLOSE</x-esp-button>
                        </x-slot>
                        <div id="tiltdropmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                    </x-control-card>

                    {{-- RELEASEDROP MOTOR --}}
                    <x-control-card showStatus>
                        <p class="text-gray-500 text-sm">RELEASEDROP MOTOR</p>
                        <x-slot name="buttons">
                            <x-esp-button class="js-esp-button" data-target="releasedropmotor" data-action="open">OPEN</x-esp-button>
                            <x-esp-button class="js-esp-button" data-target="releasedropmotor" data-action="close">CLOSE</x-esp-button>
                        </x-slot>
                        <div id="releasedropmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
                    </x-control-card>
                </div>
            </div>
        </div>
    </div>

    {{-- Het script-gedeelte blijft ongewijzigd --}}
    <script>
        // Lokale opslag voor de status
        let currentStatus = {
            // Initialiseer met de PHP data voor snelle weergave
            station: JSON.parse('{!! addslashes(json_encode($station)) !!}') || {},
            tiltdrop: JSON.parse('{!! addslashes(json_encode($tiltdrop)) !!}') || {}
        };

        const client = mqtt.connect('ws://10.11.171.126:9001');
        
        // Roep de update functie direct aan met de initiële PHP data
        document.addEventListener('DOMContentLoaded', updateAllUI);


        client.on('connect', () => {
            console.log('MQTT connected!');

            // Subscribe to motor status topics
            client.subscribe('station/status', err => {
                if (!err) console.log('Subscribed to station/status');
                // Vraag direct status op na connectie om zeker te zijn van de huidige staat
                client.publish('station/getstatus', '');
            });
            client.subscribe('tiltdrop/status', err => {
                if (!err) console.log('Subscribed to tiltdrop/status');
                // Vraag direct status op na connectie om zeker te zijn van de huidige staat
                client.publish('tiltdrop/getstatus', '');
            });
        });

        /**
         * Hulpfunctie om de kleur van een element te updaten
         */
        function updateColor(elementId, isActive) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            element.classList.remove('bg-green-500', 'bg-red-500', 'bg-gray-400', 'bg-yellow-500');

            if (isActive === true) {
                element.classList.add('bg-green-500');
            } else if (isActive === false) {
                element.classList.add('bg-red-500');
            } else {
                element.classList.add('bg-gray-400');
            }
        }
        
        /**
         * Update de Manual Toggles bovenaan de pagina
         */
        function updateToggles() {
            const station = currentStatus.station || {};
            const tiltdrop = currentStatus.tiltdrop || {};

            const stationToggle = document.getElementById('manual-switch-station');
            const tiltdropToggle = document.getElementById('manual-switch-tiltdrop');
            
            // Update Station Toggle
            if (stationToggle && typeof station.manualMode !== 'undefined') {
                // We gebruiken .checked direct, maar we vermijden om de 'change' event te triggeren
                // Dit zorgt ervoor dat de UI de status van de ESP volgt zonder onnodige publicaties.
                stationToggle.checked = station.manualMode; 
            }
            
            // Update Tiltdrop Toggle
            if (tiltdropToggle && typeof tiltdrop.manualMode !== 'undefined') {
                tiltdropToggle.checked = tiltdrop.manualMode;
            }
        }


        /**
         * Functie om ALLE UI elementen te updaten
         */
        function updateAllUI() {
            const station = currentStatus.station || {};
            const tiltdrop = currentStatus.tiltdrop || {};
            
            // 1. UPDATE TOGGLES
            updateToggles();
            
            // 2. UPDATE MOTOR STATUSES
            
            // --- STATION MOTORS ---
            const stationMotorState = station.stationMotorState ?? null;
            document.getElementById('stationmotor-status').innerText = stationMotorState === true ? 'ON' : (stationMotorState === false ? 'OFF' : 'Laden...');
            updateColor('stationmotor-status', stationMotorState);
            
            const liftMotorState = station.liftMotorState ?? null;
            document.getElementById('lifthillmotor-status').innerText = liftMotorState === true ? 'ON' : (liftMotorState === false ? 'OFF' : 'Laden...');
            updateColor('lifthillmotor-status', liftMotorState);


            // --- TILTDROP MOTORS ---
            const tiltdropMoving = tiltdrop.tiltdropMotorMoving ?? null;
            document.getElementById('tiltdropmotor-status').innerText = tiltdropMoving === true ? 'MOVING' : (tiltdropMoving === false ? 'IDLE' : 'Laden...');
            updateColor('tiltdropmotor-status', tiltdropMoving);
            
            const releasedropState = tiltdrop.releasedropMotorState ?? null;
            document.getElementById('releasedropmotor-status').innerText = releasedropState === true ? 'OPEN' : (releasedropState === false ? 'CLOSE' : 'Laden...');
            updateColor('releasedropmotor-status', releasedropState);

            
            // 3. UPDATE JSON DUMPS
            const stationOutput = document.getElementById('station-json-output');
            if (stationOutput) stationOutput.innerText = JSON.stringify(station, null, 2);

            const tiltdropOutput = document.getElementById('tiltdrop-json-output');
            if (tiltdropOutput) tiltdropOutput.innerText = JSON.stringify(tiltdrop, null, 2);
        }

        // Update status in frontend EN sla de data lokaal op
        client.on('message', (topic, payload) => {
            const message = payload.toString();
            try {
                const status = JSON.parse(message);

                // 1. Lokale status MERGEN/OPSLAAN
                if (topic === 'station/status') {
                    currentStatus.station = status; 
                } else if (topic === 'tiltdrop/status') {
                    currentStatus.tiltdrop = status;
                }
                
                // 2. Roep de centrale update functie aan
                updateAllUI();

            } catch (e) {
                console.error(`Ongeldige JSON ontvangen op ${topic} of fout bij update:`, e);
            }
        });

        // Manual toggle publishes to station/manual
        const manualToggleStation = document.getElementById('manual-switch-station');
        if (manualToggleStation) {
            manualToggleStation.addEventListener('change', () => {
                const msg = manualToggleStation.checked ? 'on' : 'off';
                client.publish('station/manual', msg);
            });
        }


        // Manual toggle publishes to tiltdrop/manual
        const manualToggleTiltdrop = document.getElementById('manual-switch-tiltdrop');
        if (manualToggleTiltdrop) {
            manualToggleTiltdrop.addEventListener('change', () => {
                const msg = manualToggleTiltdrop.checked ? 'on' : 'off';
                client.publish('tiltdrop/manual', msg);
            });
        }


        document.querySelectorAll('.js-esp-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                const action = btn.dataset.action;

                let topic;
                if (target === 'stationmotor' || target === 'lifthillmotor') {
                    topic = `station/${target}`;
                } else if (target === 'tiltdropmotor' || target === 'releasedropmotor') {
                    topic = `tiltdrop/${target}`;
                }

                client.publish(topic, action);
            });
        });
        
    </script>
</x-layout>