<x-layout>
    {{-- We hebben de MQTT client nodig --}}
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster Dashboard</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM 1: NAVIGATIE --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Navigatie Card --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <h2 class="text-2xl font-semibold text-gray-700 p-6 border-b border-gray-200">Bediening</h2>
                    <div class="p-6 space-y-4">
                        <a href="/auto-control"
                            class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            🚀 Auto Control
                        </a>
                        <a href="/manual-control"
                            class="block w-full text-center bg-gray-700 hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            🔧 Manual Control
                        </a>
                    </div>
                </div>

                {{-- Systeem Status Card (AANGEPASTE VERSIE) --}}
<div class="bg-gray-900 shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-200">Systeem Status</h2>
    <div class="space-y-4">
        
        {{-- Station Monitor --}}
        <div>
            <div class="flex justify-between items-center mb-1">
                <span class="font-medium text-gray-300">Station ESP:</span>
                <span id="station-connect-status" class="font-semibold text-gray-500 text-sm">
                    INIT...
                </span>
            </div>
            <canvas id="station-monitor" height="60" class="w-full bg-black rounded"></canvas>
        </div>

        {{-- Tiltdrop Monitor --}}
        <div>
            <div class="flex justify-between items-center mb-1">
                <span class="font-medium text-gray-300">Tiltdrop ESP:</span>
                <span id="tiltdrop-connect-status" class="font-semibold text-gray-500 text-sm">
                    INIT...
                </span>
            </div>
            <canvas id="tiltdrop-monitor" height="60" class="w-full bg-black rounded"></canvas>
        </div>

    </div>
</div>

        </div>
    </div>

    <script>
    const MQTT_HOST = "{{ env('PI_IP') }}"; 
    const client = mqtt.connect(`ws://${MQTT_HOST}:9001`);
    const logList = document.getElementById('event-log-list');
    const MAX_LOG_ENTRIES = 50;

    let stationMonitor, tiltdropMonitor;

    // === KLASSE VOOR DE HARTMONITOR ===
    class HeartbeatMonitor {
        constructor(canvasId) {
            this.canvas = document.getElementById(canvasId);
            this.ctx = this.canvas.getContext('2d');
            this.width = this.canvas.width = this.canvas.offsetWidth;
            this.height = this.canvas.height;
            
            this.baseLine = this.height / 2;
            this.color = '#374151'; // Standaard grijs (gray-700)
            this.dataPoints = Array(this.width).fill(this.baseLine);
            this.pingQueue = []; // Wachtrij voor de 'blip' animatie
            
            // --- WIJZIGING 1: GROTERE PIEK ---
            // Deze waarden zijn nu veel groter voor een dramatisch effect.
            // Baseline is 30. We gaan nu van +28 (y=58) naar -25 (y=5).
            this.blipPattern = [0, -10, -25, 0, 15, 28, 10, -5, 0];
            
            // --- WIJZIGING 2A: DE "FIX" ---
            // We onthouden alleen DAT er een ping was, niet hoeveel.
            this.blipRequested = false; 

            this.draw();
        }

        setColor(hexColor) {
            this.color = hexColor;
        }

        // --- WIJZIGING 2B: PING METHODE ---
        // Zet alleen een vlaggetje. Voegt niks meer toe aan de wachtrij.
        ping() {
            this.blipRequested = true;
        }

        draw() {
            this.ctx.clearRect(0, 0, this.width, this.height);

            // --- WIJZIGING 2C: ANIMATIE LOGICA ---
            // Alleen als er een blip is aangevraagd EN de animatie niet al loopt...
            if (this.blipRequested && this.pingQueue.length === 0) {
                this.pingQueue.push(...this.blipPattern); // ...start dan een NIEUWE blip
                this.blipRequested = false; // Vlaggetje weer omlaag
            }
            // --- Einde Wijziging ---

            // Haal het oudste datapunt weg (links)
            this.dataPoints.shift();

            // Bepaal het nieuwe datapunt (rechts)
            let newPoint;
            if (this.pingQueue.length > 0) {
                // Er is een blip bezig, neem punt uit de wachtrij
                newPoint = this.baseLine + this.pingQueue.shift();
            } else {
                // Geen blip, gebruik de basislijn met lichte ruis
                newPoint = this.baseLine + (Math.random() * 2 - 1);
            }
            this.dataPoints.push(newPoint);

            // Teken de lijn
            this.ctx.beginPath();
            this.ctx.strokeStyle = this.color;
            this.ctx.lineWidth = 2.5; // Iets dikker voor de 'mooi'
            this.ctx.shadowBlur = 4;  // Kleine gloed
            this.ctx.shadowColor = this.color;
            this.ctx.moveTo(0, this.dataPoints[0]);

            for (let i = 1; i < this.width; i++) {
                this.ctx.lineTo(i, this.dataPoints[i]);
            }
            this.ctx.stroke();
            this.ctx.shadowBlur = 0; // Reset gloed

            requestAnimationFrame(() => this.draw());
        }
    }

    // === HEARTBEAT / LWT LOGICA ===
    const HEARTBEAT_TIMEOUT_MS = 4500;
    const HEARTBEAT_TIMERS = { station: null, tiltdrop: null };

    function setConnectStatus(device, status) {
        const el = document.getElementById(`${device}-connect-status`);
        const monitor = (device === 'station') ? stationMonitor : tiltdropMonitor;
        if (!el || !monitor) return;

        // Iets fellere kleuren voor de donkere achtergrond
        el.classList.remove('text-green-400', 'text-red-400', 'text-gray-400');
        let text = 'INIT...';
        let color = '#9ca3af'; // gray-400

        if (status === 'online') {
            el.classList.add('text-green-400');
            text = 'ONLINE';
            color = '#22c55e'; // green-500
        } else if (status === 'offline') {
            el.classList.add('text-red-400');
            text = 'OFFLINE';
            color = '#f87171'; // red-400
        } else {
            el.classList.add('text-gray-400');
        }
        
        el.innerText = text;
        monitor.setColor(color);
    }

    function resetHeartbeatTimer(device) {
        if (HEARTBEAT_TIMERS[device]) {
            clearTimeout(HEARTBEAT_TIMERS[device]);
        }
        
        setConnectStatus(device, 'online');
        
        const monitor = (device === 'station') ? stationMonitor : tiltdropMonitor;
        if (monitor) monitor.ping();
        
        HEARTBEAT_TIMERS[device] = setTimeout(() => {
            console.warn(`Heartbeat timeout for ${device}.`);
            setConnectStatus(device, 'offline');
            addLogEntry(`TIMEOUT`, `${device} is offline. Geen heartbeat ontvangen.`, 'error');
        }, HEARTBEAT_TIMEOUT_MS);
    }

    // === LOG FUNCTIE (ONGEWIJZIGD) ===
    function addLogEntry(topic, message, level = 'info') {
        if (logList.children.length > MAX_LOG_ENTRIES) {
            logList.removeChild(logList.firstChild);
        }
        const li = document.createElement('li');
        const timestamp = new Date().toLocaleTimeString();
        let colorClass = 'text-gray-700';
        if (level === 'error') colorClass = 'text-red-600';
        if (level === 'warn') colorClass = 'text-yellow-600';
        li.className = `p-2 rounded ${level === 'info' ? 'bg-gray-50' : 'bg-red-50'} ${colorClass}`;
        li.innerHTML = `
            <span class="text-gray-500">[${timestamp}]</span> 
            <strong class="text-black">${topic}:</strong> ${message}
        `;
        logList.appendChild(li);
        logList.parentElement.scrollTop = logList.parentElement.scrollHeight;
    }

    // === MQTT CONNECTIE (ONGEWIJZIGD) ===
    client.on('connect', () => {
        console.log('MQTT connected!');
        client.subscribe('rollercoaster/station/status');
        client.subscribe('rollercoaster/tiltdrop/status');
        client.subscribe('rollercoaster/log');
        client.subscribe('rollercoaster/estop');
        addLogEntry('MQTT', 'Verbonden met de broker.', 'success');
        
        if (HEARTBEAT_TIMERS['station']) clearTimeout(HEARTBEAT_TIMERS['station']);
        HEARTBEAT_TIMERS['station'] = setTimeout(() => {
            setConnectStatus('station', 'offline');
            addLogEntry(`TIMEOUT`, `Station ESP was nooit online.`, 'error');
        }, HEARTBEAT_TIMEOUT_MS);

        if (HEARTBEAT_TIMERS['tiltdrop']) clearTimeout(HEARTBEAT_TIMERS['tiltdrop']);
        HEARTBEAT_TIMERS['tiltdrop'] = setTimeout(() => {
            setConnectStatus('tiltdrop', 'offline');
            addLogEntry(`TIMEOUT`, `Tiltdrop ESP was nooit online.`, 'error');
        }, HEARTBEAT_TIMEOUT_MS);
    });

    // === MQTT BERICHTEN (ONGEWIJZIGD) ===
    client.on('message', (topic, payload) => {
        const msg = payload.toString().trim();
        if (topic === 'rollercoaster/station/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('station');
            return;
        }
        if (topic === 'rollercoaster/tiltdrop/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('tiltdrop');
            return;
        }
        if (topic === 'rollercoaster/log') {
            addLogEntry('LOG', msg);
        }
        if (topic === 'rollercoaster/estop' && msg.toLowerCase() === 'true') {
            addLogEntry('E-STOP', 'EMERGENCY STOP GEACTIVEERD!', 'error');
        }
    });

    // === INIT (ONGEWIJZIGD) ===
    document.addEventListener('DOMContentLoaded', () => {
        stationMonitor = new HeartbeatMonitor('station-monitor');
        tiltdropMonitor = new HeartbeatMonitor('tiltdrop-monitor');
        setConnectStatus('station', 'unknown');
        setConnectStatus('tiltdrop', 'unknown');
        const firstLog = logList.querySelector('li');
        if (firstLog && firstLog.textContent.includes('Wachten')) {
            logList.removeChild(firstLog);
        }
        addLogEntry('SYSTEM', 'Pagina geladen. Bezig met verbinden...');
    });

    client.publish('rollercoaster/test', 'hello from browser');

</script>
</x-layout>