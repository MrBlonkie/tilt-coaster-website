<x-layout>

<x-toggle name="monitoring" id="monitoring"/>

{{-- ONBOARD LED --}}
<x-control-card showStatus class="max-w-md mx-auto">
    <p class="text-gray-500 text-sm">ONBOARD LED</p>
    <x-slot name="buttons">
        <x-esp-button data-target="led" data-action="on">ON</x-esp-button>
        <x-esp-button data-target="led" data-action="off">OFF</x-esp-button>
    </x-slot>
    <div id="led-status" style="width:30px; height:30px; border-radius:50%; background-color:grey; margin-top:10px;"></div>
    <div id="led-status-text">Laden...</div>
</x-control-card>

{{-- STATION MOTOR --}}
<x-control-card showStatus class="max-w-md mx-auto mt-4">
    <p class="text-gray-500 text-sm">STATION MOTOR</p>
    <x-slot name="buttons">
        <x-esp-button data-target="stationmotor" data-action="on">ON</x-esp-button>
        <x-esp-button data-target="stationmotor" data-action="off">OFF</x-esp-button>
    </x-slot>
    <div id="stationmotor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">Laden...</div>
</x-control-card>

{{-- LIFTHILL MOTOR --}}
<x-control-card showStatus class="max-w-md mx-auto mt-4">
    <p class="text-gray-500 text-sm">LIFTHILL MOTOR</p>
    <x-slot name="buttons">
        <x-esp-button data-target="lifthillmotor" data-action="on">ON</x-esp-button>
        <x-esp-button data-target="lifthillmotor" data-action="off">OFF</x-esp-button>
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

// LED
async function postLed(state){
    await fetch(`/led/${state}`, {
        method:'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateLedStatus();
}

async function updateLedStatus() {
    try {
        const response = await fetch('/manual-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('led-status');
        const textDiv = document.getElementById('led-status-text');

        statusDiv.style.backgroundColor = data.onboardLED ? "green" : "red";
        textDiv.textContent = data.onboardLED ? "LED staat aan" : "LED staat uit";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('led-status-text').textContent = 'Fout bij ophalen status';
        document.getElementById('led-status').style.backgroundColor = "grey";
    }
}

// STATION MOTOR
async function postStationMotor(state){
    await fetch(`/stationmotor/${state}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateStationMotorStatus();
}

async function updateStationMotorStatus() {
    try {
        const response = await fetch('/manual-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('stationmotor-status');

        statusDiv.style.backgroundColor = data.stationMotor ? "green" : "red";
        statusDiv.textContent = data.stationMotor ? "Motor draait" : "Motor staat stil";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('led-status-text').textContent = 'Fout bij ophalen status';
        document.getElementById('led-status').style.backgroundColor = "grey";
    }
}

// LIFTHILL MOTOR
async function postLiftMotor(state){
    await fetch(`/lifthillmotor/${state}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateLifthillMotorStatus();
}

async function updateLifthillMotorStatus() {
    try {
        const response = await fetch('/manual-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('lifthillmotor-status');

        statusDiv.style.backgroundColor = data.lifthillMotor ? "green" : "red";
        statusDiv.textContent = data.lifthillMotor ? "Motor draait" : "Motor staat stil";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('led-status-text').textContent = 'Fout bij ophalen status';
        document.getElementById('led-status').style.backgroundColor = "grey";
    }
}

// toggle monitoring aan/uit
document.getElementById('monitoring').addEventListener('change', function(e) {
    if (e.target.checked) {
        updateLedStatus();
        updateStationMotorStatus();
        updateLifthillMotorStatus();
        updateInterval = setInterval(() => {
            updateLedStatus();
            updateStationMotorStatus();
            updateLifthillMotorStatus();

        }, 200);
    } else {
        clearInterval(updateInterval);
        updateInterval = null;
    }
});

window.addEventListener('beforeunload', () => {
    if (updateInterval) clearInterval(updateInterval);
});


</script>

</x-layout>
