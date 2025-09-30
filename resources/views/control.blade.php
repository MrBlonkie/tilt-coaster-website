<x-layout>
    <h1>CONTROL</h1>

<div id="led-status" style="width:30px; height:30px; border-radius:50%; background-color:grey; margin-top:20px; margin-bottom: 20px;"></div>
<div id="led-status-text">laden...</div>




<x-esp-button onclick="postLed('on')">LED On</x-esp-button>
<x-esp-button onclick="postLed('off')">LED Off</x-esp-button>

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
setInterval(updateLedStatus, 200);
updateLedStatus(); // meteen eerste keer ophalen
</script>



</x-layout>

