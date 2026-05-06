<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- HERO --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="h-2 w-2 rounded-full bg-[var(--color-ember)] animate-pulse"></span>
                <span class="text-xs font-mono text-gray-500 uppercase tracking-widest">Live systeem</span>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Dashboard</h1>
            <p class="text-gray-500 mt-1 text-sm">Rollercoaster besturingscentrum — De Vliegende Vlaeminck</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM 1: NAVIGATIE & SYSTEEM STATUS --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Quick Actions --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800">
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Bediening</h2>
                    </div>
                    <div class="p-4 space-y-2.5">
                        <a href="/auto-control"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-[var(--color-primary)] hover:bg-[var(--color-ember)] text-white font-semibold rounded-lg text-sm transition-all duration-200 group">
                            <svg class="h-4 w-4 shrink-0 text-[var(--color-ember)] group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Auto Control
                        </a>
                        <a href="/manual-control"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-[var(--color-accent)] hover:bg-[var(--color-highlight)] text-white font-semibold rounded-lg text-sm transition-all duration-200">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            Manual Control
                        </a>
                    </div>
                </div>

                {{-- Systeem Status --}}
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Systeem Status</h2>
                        <span class="text-xs font-mono text-gray-600">ESP NODES</span>
                    </div>
                    <div class="p-4 space-y-5">

                        {{-- Station Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Station</span>
                                <span id="station-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="station-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Tiltdrop Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Tiltdrop</span>
                                <span id="tiltdrop-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="tiltdrop-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Brakes Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Brakes</span>
                                <span id="brakes-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="brakes-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                        {{-- Switchtrack Monitor --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-mono text-gray-500 uppercase tracking-wide">Switchtrack</span>
                                <span id="switchtrack-connect-status" class="text-xs font-mono font-bold text-gray-600">INIT...</span>
                            </div>
                            <canvas id="switchtrack-monitor" height="48" class="w-full rounded bg-black/60 border border-gray-800"></canvas>
                        </div>

                    </div>
                </div>

            </div>

            {{-- KOLOM 2: EVENT LOG --}}
            <div class="lg:col-span-2">
                <div class="bg-[var(--color-primary)] border border-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                    {{-- Terminal header --}}
                    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <div class="h-3 w-3 rounded-full bg-red-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-yellow-500/70"></div>
                            <div class="h-3 w-3 rounded-full bg-green-500/70"></div>
                        </div>
                        <h2 class="text-xs font-semibold text-gray-300 uppercase tracking-widest">Systeem Log</h2>
                        <span class="ml-auto text-xs font-mono text-gray-600" id="log-count">0 events</span>
                    </div>
                    <div class="p-3 flex-1">
                        <ul id="event-log-list" class="space-y-px h-[560px] overflow-y-auto font-mono text-xs pr-1">
                            <li class="py-1 px-2 text-gray-600">Wachten op verbinding...</li>
                        </ul>
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

    let stationMonitor, tiltdropMonitor, brakesMonitor, switchtrackMonitor;

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

        setColor(hexColor) { this.color = hexColor; }

        ping() { this.blipRequested = true; }

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
            this.ctx.lineWidth = 2;
            this.ctx.shadowBlur = 5;
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

    const HEARTBEAT_TIMEOUT_MS = 4500;
    const HEARTBEAT_TIMERS = { station: null, tiltdrop: null, brakes: null, switchtrack: null };

    function setConnectStatus(device, status) {
        const el = document.getElementById(`${device}-connect-status`);
        const monitorMap = {
            station: stationMonitor,
            tiltdrop: tiltdropMonitor,
            brakes: brakesMonitor,
            switchtrack: switchtrackMonitor,
        };
        const monitor = monitorMap[device];
        if (!el || !monitor) return;

        el.classList.remove('text-emerald-400', 'text-red-400', 'text-gray-500', 'text-gray-600');

        if (status === 'online') {
            el.classList.add('text-emerald-400');
            el.innerText = 'ONLINE';
            monitor.setColor('#34d399');
        } else if (status === 'offline') {
            el.classList.add('text-red-400');
            el.innerText = 'OFFLINE';
            monitor.setColor('#f87171');
        } else {
            el.classList.add('text-gray-500');
            el.innerText = 'INIT...';
            monitor.setColor('#4b5563');
        }
    }

    function resetHeartbeatTimer(device) {
        if (HEARTBEAT_TIMERS[device]) clearTimeout(HEARTBEAT_TIMERS[device]);
        setConnectStatus(device, 'online');
        const monitorMap = {
            station: stationMonitor,
            tiltdrop: tiltdropMonitor,
            brakes: brakesMonitor,
            switchtrack: switchtrackMonitor,
        };
        const monitor = monitorMap[device];
        if (monitor) monitor.ping();
        HEARTBEAT_TIMERS[device] = setTimeout(() => {
            setConnectStatus(device, 'offline');
            addLogEntry('TIMEOUT', `${device} is offline. Geen heartbeat ontvangen.`, 'error');
        }, HEARTBEAT_TIMEOUT_MS);
    }

    function addLogEntry(topic, message, level = 'info') {
        if (!logList) return;
        if (logList.children.length >= MAX_LOG_ENTRIES) {
            logList.removeChild(logList.firstChild);
        }

        const li = document.createElement('li');
        const timestamp = new Date().toLocaleTimeString('nl-BE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        let topicColor = 'text-cyan-400';
        let msgColor = 'text-gray-300';
        let rowBg = 'hover:bg-white/5';

        if (level === 'error') {
            topicColor = 'text-red-400';
            msgColor = 'text-red-300';
            rowBg = 'bg-red-900/20 hover:bg-red-900/30';
        } else if (level === 'warn') {
            topicColor = 'text-yellow-400';
            msgColor = 'text-yellow-200';
            rowBg = 'hover:bg-white/5';
        }

        li.className = `py-0.5 px-2 rounded transition-colors ${rowBg}`;
        li.innerHTML = `<span class="text-gray-600">[${timestamp}]</span> <span class="${topicColor} font-bold">${topic}</span><span class="text-gray-700">:</span> <span class="${msgColor}">${message}</span>`;

        logList.appendChild(li);
        logList.scrollTop = logList.scrollHeight;

        const countEl = document.getElementById('log-count');
        if (countEl) countEl.textContent = `${logList.children.length} events`;
    }

    client.on('connect', () => {
        client.subscribe('rollercoaster/station/status');
        client.subscribe('rollercoaster/tiltdrop/status');
        client.subscribe('rollercoaster/brakes/status');
        client.subscribe('rollercoaster/switchtrack/status');
        client.subscribe('rollercoaster/log');
        client.subscribe('rollercoaster/estop');
        addLogEntry('MQTT', 'Verbonden met de broker.');

        ['station', 'tiltdrop', 'brakes', 'switchtrack'].forEach(dev => {
            if (HEARTBEAT_TIMERS[dev]) clearTimeout(HEARTBEAT_TIMERS[dev]);
            HEARTBEAT_TIMERS[dev] = setTimeout(() => {
                setConnectStatus(dev, 'offline');
                addLogEntry('TIMEOUT', `${dev} ESP was nooit online.`, 'error');
            }, HEARTBEAT_TIMEOUT_MS);
        });
    });

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

    document.addEventListener('DOMContentLoaded', () => {
        stationMonitor = new HeartbeatMonitor('station-monitor');
        tiltdropMonitor = new HeartbeatMonitor('tiltdrop-monitor');
        brakesMonitor = new HeartbeatMonitor('brakes-monitor');
        switchtrackMonitor = new HeartbeatMonitor('switchtrack-monitor');

        setConnectStatus('station', 'unknown');
        setConnectStatus('tiltdrop', 'unknown');
        setConnectStatus('brakes', 'unknown');
        setConnectStatus('switchtrack', 'unknown');

        const firstLog = logList?.querySelector('li');
        if (firstLog?.textContent.includes('Wachten')) logList.removeChild(firstLog);
        addLogEntry('SYSTEM', 'Pagina geladen. Bezig met verbinden...');
    });
</script>
</x-layout>
