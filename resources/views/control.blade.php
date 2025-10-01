<x-layout>

<x-toggle name='monitoring' id='monitoring'/>

<x-control-card showStatus class="max-w-md mx-auto">
    <p class="text-gray-500 text-sm">
        ONBOARD LED
    </p>

    <x-slot name="buttons">
        <x-esp-button onclick="postLed('on')" class="group inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
            ON <span aria-hidden="true" class="block transition-all group-hover:ms-0.5 rtl:rotate-180"></span>
        </x-esp-button>
        <x-esp-button onclick="postLed('off')" class="group inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
            OFF <span aria-hidden="true" class="block transition-all group-hover:ms-0.5 rtl:rotate-180"></span>
        </x-esp-button>
    </x-slot>
</x-control-card>


<x-control-card showStatus class="max-w-md mx-auto mt-4">
    <p class="text-gray-500 text-sm">
        STATION MOTOR
    </p>

    <x-slot name="buttons">
        <x-esp-button onclick="postMotor('on')" class="group inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
            ON <span aria-hidden="true" class="block transition-all group-hover:ms-0.5 rtl:rotate-180"></span>
        </x-esp-button>
        <x-esp-button onclick="postMotor('off')" class="group inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
            OFF <span aria-hidden="true" class="block transition-all group-hover:ms-0.5 rtl:rotate-180"></span>
        </x-esp-button>
    </x-slot>

    <div id="motor-status" class="mt-2 p-2 rounded text-white bg-gray-400 text-center">
        Laden...
    </div>
</x-control-card>



<script>

//ONBOARD LED SCRIPT
let updateInterval = null;

async function postLed(state) {
    const res = await fetch(`/led/${state}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    const data = await res.json();
    console.log(data);
    updateLedStatus(); // meteen status updaten na POST
}

async function updateLedStatus() {
    try {
        const response = await fetch('/control/status'); 
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

//MOTOR SCRIPT
async function postMotor(state) {
    const res = await fetch(`/motor/${state}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    const data = await res.json();
    console.log(data);
    updateMotorStatus(); // meteen status updaten na POST
}

async function updateMotorStatus() {
    try {
        const response = await fetch('/control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const motorDiv = document.getElementById('motor-status');

        if (data.stationMotor) {
            motorDiv.textContent = "Motor draait";
            motorDiv.style.backgroundColor = "green";
        } else {
            motorDiv.textContent = "Motor gestopt";
            motorDiv.style.backgroundColor = "red";
        }

    } catch (error) {
        console.error('Fout bij ophalen motor status:', error);
        const motorDiv = document.getElementById('motor-status');
        motorDiv.textContent = "Fout bij ophalen status";
        motorDiv.style.backgroundColor = "grey";
    }
}

// toggle monitoring aan/uit
document.getElementById('monitoring').addEventListener('change', function(e) {
    if (e.target.checked) {
        updateLedStatus();
        updateMotorStatus();
        updateInterval = setInterval(() => {
            updateLedStatus();
            updateMotorStatus();
        }, 2000);
    } else {
        clearInterval(updateInterval);
        updateInterval = null;
    }
});

</script>



</x-layout>

