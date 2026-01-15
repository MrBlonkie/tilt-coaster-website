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

        {{-- Brakes Monitor --}}
        <div>
            <div class="flex justify-between items-center mb-1">
                <span class="font-medium text-gray-300">Brakes ESP:</span>
                <span id="brakes-connect-status" class="font-semibold text-gray-500 text-sm">
                    INIT...
                </span>
            </div>
            <canvas id="brakes-monitor" height="60" class="w-full bg-black rounded"></canvas>
        </div>

        {{-- Switchtrack Monitor --}}
        <div>
            <div class="flex justify-between items-center mb-1">
                <span class="font-medium text-gray-300">Switchtrack ESP:</span>
                <span id="switchtrack-connect-status" class="font-semibold text-gray-500 text-sm">
                    INIT...
                </span>
            </div>
            <canvas id="switchtrack-monitor" height="60" class="w-full bg-black rounded"></canvas>
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

    let stationMonitor, tiltdropMonitor, switchtrackMonitor;

    // === KLASSE VOOR DE HARTMONITOR ===
    class HeartbeatMonitor {
        constructor(canvasId) {
            this.canvas = document.getElementById(canvasId);
            this.ctx = this.canvas.getContext('2d');
            this.width = this.canvas.width = this.canvas.offsetWidth;
            this.height = this.canvas.height;
            
            this.baseLine = this.height / 2;
            this.color = '#374151'; 
            this.dataPoints = Array(this.width).fill(this.baseLine);
            this.pingQueue = [];
            
            this.blipPattern = [0, -10, -25, 0, 15, 28, 10, -5, 0];
            this.blipRequested = false;

            this.draw();
        }

        setColor(hexColor) {
            this.color = hexColor;
        }

        ping() {
            this.blipRequested = true;
        }

        draw() {
            this.ctx.clearRect(0, 0, this.width, this.height);

            if (this.blipRequested && this.pingQueue.length === 0) {
                this.pingQueue.push(...this.blipPattern);
                this.blipRequested = false;
            }

            this.dataPoints.shift();

            let newPoint;
            if (this.pingQueue.length > 0) {
                newPoint = this.baseLine + this.pingQueue.shift();
            } else {
                newPoint = this.baseLine + (Math.random() * 2 - 1);
            }
            this.dataPoints.push(newPoint);

            this.ctx.beginPath();
            this.ctx.strokeStyle = this.color;
            this.ctx.lineWidth = 2.5;
            this.ctx.shadowBlur = 4;
            this.ctx.shadowColor = this.color;
            this.ctx.moveTo(0, this.dataPoints[0]);

            for (let i = 1; i < this.width; i++) {
                this.ctx.lineTo(i, this.dataPoints[i]);
            }
            this.ctx.stroke();
            this.ctx.shadowBlur = 0;

            requestAnimationFrame(() => this.draw());
        }
    }

    // === HEARTBEAT / LWT LOGICA ===
    const HEARTBEAT_TIMEOUT_MS = 4500;
    const HEARTBEAT_TIMERS = { station: null, tiltdrop: null, switchtrack: null };

    function setConnectStatus(device, status) {
        const el = document.getElementById(`${device}-connect-status`);
        const monitor =
            device === 'station' ? stationMonitor :
            device === 'tiltdrop' ? tiltdropMonitor :
            device === 'brakes' ? brakesMonitor :
            device === 'switchtrack' ? switchtrackMonitor :
            null;

        if (!el || !monitor) return;

        el.classList.remove('text-green-400', 'text-red-400', 'text-gray-400');
        let text = 'INIT...';
        let color = '#9ca3af';

        if (status === 'online') {
            el.classList.add('text-green-400');
            text = 'ONLINE';
            color = '#22c55e';
        } else if (status === 'offline') {
            el.classList.add('text-red-400');
            text = 'OFFLINE';
            color = '#f87171';
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

        const monitor =
            device === 'station' ? stationMonitor :
            device === 'tiltdrop' ? tiltdropMonitor :
            device === 'brakes' ? brakesMonitor :
            device === 'switchtrack' ? switchtrackMonitor :
            null;

        if (monitor) monitor.ping();
        
        HEARTBEAT_TIMERS[device] = setTimeout(() => {
            console.warn(`Heartbeat timeout for ${device}.`);
            setConnectStatus(device, 'offline');
            addLogEntry(`TIMEOUT`, `${device} is offline. Geen heartbeat ontvangen.`, 'error');
        }, HEARTBEAT_TIMEOUT_MS);
    }

    // === LOG FUNCTIE ===
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

    // === MQTT CONNECTIE ===
    client.on('connect', () => {
        console.log('MQTT connected!');
        client.subscribe('rollercoaster/station/status');
        client.subscribe('rollercoaster/tiltdrop/status');
        client.subscribe('rollercoaster/brakes/status');
        client.subscribe('rollercoaster/switchtrack/status');
        client.subscribe('rollercoaster/log');
        client.subscribe('rollercoaster/estop');
        addLogEntry('MQTT', 'Verbonden met de broker.', 'success');

        ['station','tiltdrop', 'brakes', 'switchtrack'].forEach(dev => {
            if (HEARTBEAT_TIMERS[dev]) clearTimeout(HEARTBEAT_TIMERS[dev]);
            HEARTBEAT_TIMERS[dev] = setTimeout(() => {
                setConnectStatus(dev, 'offline');
                addLogEntry(`TIMEOUT`, `${dev} ESP was nooit online.`, 'error');
            }, HEARTBEAT_TIMEOUT_MS);
        });
    });

    // === MQTT MESSAGES ===
    client.on('message', (topic, payload) => {
        const msg = payload.toString().trim();

        if (topic === 'rollercoaster/station/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('station'); return;
        }
        if (topic === 'rollercoaster/tiltdrop/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('tiltdrop'); return;
        }
        if (topic === 'rollercoaster/brakes/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('brakes'); return;
        }
        if (topic === 'rollercoaster/switchtrack/status' && msg.toLowerCase() === 'online') {
            resetHeartbeatTimer('switchtrack'); return;
        }

        if (topic === 'rollercoaster/log') {
            addLogEntry('LOG', msg);
        }
        if (topic === 'rollercoaster/estop' && msg.toLowerCase() === 'true') {
            addLogEntry('E-STOP', 'EMERGENCY STOP GEACTIVEERD!', 'error');
        }
    });

    // === INIT ===
    document.addEventListener('DOMContentLoaded', () => {
        stationMonitor = new HeartbeatMonitor('station-monitor');
        tiltdropMonitor = new HeartbeatMonitor('tiltdrop-monitor');
        brakesMonitor = new HeartbeatMonitor('brakes-monitor');
        switchtrackMonitor = new HeartbeatMonitor('switchtrack-monitor');

        setConnectStatus('station', 'unknown');
        setConnectStatus('tiltdrop', 'unknown');
        setConnectStatus('brakes', 'unknown');
        setConnectStatus('switchtrack', 'unknown');

        const firstLog = logList.querySelector('li');
        if (firstLog && firstLog.textContent.includes('Wachten')) {
            logList.removeChild(firstLog);
        }
        addLogEntry('SYSTEM', 'Pagina geladen. Bezig met verbinden...');
    });

    client.publish('rollercoaster/test', 'hello from browser');

</script>
</x-layout>
