<x-layout>
    <h1>CONTROL</h1>

<div id="led-status" style="width:30px; height:30px; border-radius:50%; background-color:grey; margin-top:20px;"></div>

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
}
</script>




</x-layout>

