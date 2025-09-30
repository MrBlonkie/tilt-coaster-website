<x-layout>

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



<script>
async function postLed(state) {
    const res = await fetch(`/control/${state}`, {
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
        const response = await fetch('/esp/status'); 
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

// iedere 2 seconden updaten
setInterval(updateLedStatus, 1000);
updateLedStatus(); // meteen eerste keer ophalen
</script>



</x-layout>

