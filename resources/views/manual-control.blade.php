<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <x-toggle name="manual-switch-station" id="manual-switch-station">manual switch station</x-toggle>
    <x-toggle name="manual-switch-tiltdrop" id="manual-switch-tiltdrop">manual switch tiltdrop</x-toggle>

    {{-- STATION MOTOR --}}
    <x-control-card showStatus class="max-w-md mx-auto mt-4">
        <p class="text-gray-500 text-sm">STATION MOTOR</p>
        <x-slot name="buttons">
            <x-esp-button class="js-esp-button" data-target="stationmotor" data-action="on">ON</x-esp-button>
            <x-esp-button class="js-esp-button" data-target="stationmotor" data-action="off">OFF</x-esp-button>
        </x-slot>
        <div id="stationmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
    </x-control-card>

    {{-- LIFTHILL MOTOR --}}
    <x-control-card showStatus class="max-w-md mx-auto mt-4">
        <p class="text-gray-500 text-sm">LIFTHILL MOTOR</p>
        <x-slot name="buttons">
            <x-esp-button class="js-esp-button" data-target="lifthillmotor" data-action="on">ON</x-esp-button>
            <x-esp-button class="js-esp-button" data-target="lifthillmotor" data-action="off">OFF</x-esp-button>
        </x-slot>
        <div id="lifthillmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
    </x-control-card>

    {{-- TILTDROP MOTOR --}}
    <x-control-card showStatus class="max-w-md mx-auto mt-4">
        <p class="text-gray-500 text-sm">TILTDROP MOTOR</p>
        <x-slot name="buttons">
            <x-esp-button class="js-esp-button" data-target="tiltdropmotor" data-action="open">OPEN</x-esp-button>
            <x-esp-button class="js-esp-button" data-target="tiltdropmotor" data-action="close">CLOSE</x-esp-button>
        </x-slot>
        <div id="tiltdropmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
    </x-control-card>

    {{-- RELEASEDROP MOTOR --}}
    <x-control-card showStatus class="max-w-md mx-auto mt-4">
        <p class="text-gray-500 text-sm">RELEASEDROP MOTOR</p>
        <x-slot name="buttons">
            <x-esp-button class="js-esp-button" data-target="releasedropmotor" data-action="open">OPEN</x-esp-button>
            <x-esp-button class="js-esp-button" data-target="releasedropmotor" data-action="close">CLOSE</x-esp-button>
        </x-slot>
        <div id="releasedropmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
    </x-control-card>

    <script>
        const client = mqtt.connect('ws://10.11.171.126:9001');

        client.on('connect', () => {
            console.log('MQTT connected!');

            // Subscribe to motor status topics
            client.subscribe('station/status', err => {
                if (!err) console.log('Subscribed to station/status');
            });
            client.subscribe('tiltdrop/status', err => {
                if (!err) console.log('Subscribed to tiltdrop/status');
            });
        });

        // Update status in frontend
        client.on('message', (topic, payload) => {
            const message = payload.toString();
            try {
                const status = JSON.parse(message);
                document.getElementById('stationmotor-status').innerText = status.stationMotorManual ? 'ON' : 'OFF';
                document.getElementById('lifthillmotor-status').innerText = status.liftMotorManual ? 'ON' : 'OFF';
                document.getElementById('tiltdropmotor-status').innerText = status.tiltdropMotorState ? 'OPEN' :
                    'CLOSE';
                document.getElementById('releasedropmotor-status').innerText = status.releasedropMotorState ?
                    'OPEN' : 'CLOSE';
            } catch (e) {
                console.error('Invalid JSON', e);
            }
        });

        // Manual toggle publishes to station/manual
        const manualToggle = document.getElementById('manual-switch-station');
        manualToggle.addEventListener('change', () => {
            const msg = manualToggle.checked ? 'on' : 'off';
            client.publish('station/manual', msg);
        });

        // Manual toggle publishes to tiltdrop/manual
        const manualToggle1 = document.getElementById('manual-switch-tiltdrop');
        manualToggle1.addEventListener('change', () => {
            const msg = manualToggle1.checked ? 'on' : 'off';
            client.publish('tiltdrop/manual', msg);
        });

        document.querySelectorAll('.js-esp-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.target; 
        const action = btn.dataset.action;

        let topic;
        if(target === 'stationmotor' || target === 'lifthillmotor') {
            topic = `station/${target}`;
        } else if(target === 'tiltdropmotor' || target === 'releasedropmotor') {
            topic = `tiltdrop/${target}`; // past bij jouw ESP code
        }

        client.publish(topic, action);
    });
});


    </script>
</x-layout>
