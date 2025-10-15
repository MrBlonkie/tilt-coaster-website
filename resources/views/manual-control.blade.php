<x-layout>

<x-toggle name="monitoring" id="monitoring">monitoring</x-toggle>

<x-toggle name="manual-switch" id="manual-switch">manual switch</x-toggle>

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

<script>
let updateInterval = null;

document.querySelectorAll('.js-esp-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.target;
        const action = btn.dataset.action;

        switch(target){
            case 'led':
                postLed(action);
                break;
            case 'stationmotor':
                postStationMotor(action);
                break;
            case 'lifthillmotor':
                postLiftMotor(action);
                break;
        }
    });
});

// STATION MOTOR
async function postStationMotor(state){
    await fetch(`/manual/stationmotor/${state}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateStationMotorStatus();
}

async function updateStationMotorStatus() {
    try {
        const response = await fetch('/auto-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('stationmotor-status');

        statusDiv.style.backgroundColor = data.stationMotorManual ? "green" : "red";
        statusDiv.textContent = data.stationMotorManual ? "Motor draait" : "Motor staat stil";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('led-status-text').textContent = 'Fout bij ophalen status';
        document.getElementById('led-status').style.backgroundColor = "grey";
    }
}

// LIFTHILL MOTOR
async function postLiftMotor(state){
    await fetch(`/manual/lifthillmotor/${state}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateLifthillMotorStatus();
}

async function updateLifthillMotorStatus() {
    try {
        const response = await fetch('/auto-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('lifthillmotor-status');

        statusDiv.style.backgroundColor = data.liftMotorManual ? "green" : "red";
        statusDiv.textContent = data.liftMotorManual ? "Motor draait" : "Motor staat stil";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('led-status-text').textContent = 'Fout bij ophalen status';
        document.getElementById('led-status').style.backgroundColor = "grey";
    }
}

// toggle monitoring aan/uit
document.getElementById('monitoring').addEventListener('change', function(e) {
    if (e.target.checked) {
        updateStationMotorStatus();
        updateLifthillMotorStatus();
        updateInterval = setInterval(() => {
            updateStationMotorStatus();
            updateLifthillMotorStatus();

        }, 500);
    } else {
        clearInterval(updateInterval);
        updateInterval = null;
    }
});

window.addEventListener('beforeunload', () => {
    if (updateInterval) clearInterval(updateInterval);
});

// toggle manual mode aan/uit
document.getElementById('manual-switch').addEventListener('change', async function(e) {
    const state = e.target.checked ? 'on' : 'off';

    try {
        const response = await fetch(`/manual/${state}`); // <-- GEEN POST, gewoon GET
        if (!response.ok) throw new Error('Fout bij verzenden manual mode');

        const data = await response.json();
        console.log('Manual mode:', data);

        // Optioneel: UI feedback
        const text = e.target.checked ? 'Manual mode ingeschakeld' : 'Manual mode uitgeschakeld';
        console.log(text);
    } catch (error) {
        console.error('Fout bij togglen manual mode:', error);
    }
});


async function updateManualToggle() {
    try {
        const response = await fetch('/auto-control/status');
        const data = await response.json();
        document.getElementById('manual-switch').checked = data.manualMode;
    } catch (err) {
        console.error('Kon manual status niet ophalen:', err);
    }
}

// aanroepen bij paginalaad
updateManualToggle();

</script>

</x-layout>
