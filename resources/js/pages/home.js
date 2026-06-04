import mqtt from 'mqtt';
import { createHeartbeatManager } from '../heartbeat.js';

const protocol = location.protocol === 'https:' ? 'wss' : 'ws';
const client = mqtt.connect(`${protocol}://${window.MQTT_HOST}:9001`);
const logList = document.getElementById('event-log-list');
const MAX_LOG_ENTRIES = 50;

let stationMonitor, tiltdropMonitor, brakesMonitor, switchtrackMonitor;
const monitorMap = {};

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
        const newPoint = this.pingQueue.length > 0
            ? this.baseLine + this.pingQueue.shift()
            : this.baseLine + (Math.random() * 2 - 1);
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

function setConnectStatus(device, status) {
    const el = document.getElementById(`${device}-connect-status`);
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

function nowTime() {
    return new Date().toLocaleTimeString('nl-BE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

const heartbeat = createHeartbeatManager(
    device => {
        setConnectStatus(device, 'online');
        monitorMap[device]?.ping();
        const hbEl = document.getElementById(`last-hb-${device}`);
        if (hbEl) hbEl.textContent = nowTime();
    },
    device => {
        setConnectStatus(device, 'offline');
        addLogEntry('TIMEOUT', `${device} is offline. Geen heartbeat ontvangen.`, 'error');
    }
);

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

    const brokerEl = document.getElementById('mqtt-broker-status');
    const sinceEl = document.getElementById('mqtt-connected-since');
    if (brokerEl) {
        brokerEl.textContent = 'ONLINE';
        brokerEl.classList.remove('text-gray-400', 'text-red-400');
        brokerEl.classList.add('text-emerald-400');
    }
    if (sinceEl) sinceEl.textContent = nowTime();

    heartbeat.initAll(['station', 'tiltdrop', 'brakes', 'switchtrack']);
});

client.on('message', (topic, payload) => {
    const msg = payload.toString().trim();

    if (topic === 'rollercoaster/station/status' && msg.toLowerCase() === 'online') {
        heartbeat.reset('station'); return;
    }
    if (topic === 'rollercoaster/tiltdrop/status' && msg.toLowerCase() === 'online') {
        heartbeat.reset('tiltdrop'); return;
    }
    if (topic === 'rollercoaster/brakes/status' && msg.toLowerCase() === 'online') {
        heartbeat.reset('brakes'); return;
    }
    if (topic === 'rollercoaster/switchtrack/status' && msg.toLowerCase() === 'online') {
        heartbeat.reset('switchtrack'); return;
    }
    if (topic === 'rollercoaster/log') {
        addLogEntry('LOG', msg);
    }
    if (topic === 'rollercoaster/estop' && msg.toLowerCase() === 'true') {
        addLogEntry('E-STOP', 'EMERGENCY STOP GEACTIVEERD!', 'error');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    stationMonitor     = new HeartbeatMonitor('station-monitor');
    tiltdropMonitor    = new HeartbeatMonitor('tiltdrop-monitor');
    brakesMonitor      = new HeartbeatMonitor('brakes-monitor');
    switchtrackMonitor = new HeartbeatMonitor('switchtrack-monitor');

    monitorMap.station     = stationMonitor;
    monitorMap.tiltdrop    = tiltdropMonitor;
    monitorMap.brakes      = brakesMonitor;
    monitorMap.switchtrack = switchtrackMonitor;

    setConnectStatus('station', 'unknown');
    setConnectStatus('tiltdrop', 'unknown');
    setConnectStatus('brakes', 'unknown');
    setConnectStatus('switchtrack', 'unknown');

    const firstLog = logList?.querySelector('li');
    if (firstLog?.textContent.includes('Wachten')) logList.removeChild(firstLog);
    addLogEntry('SYSTEM', 'Pagina geladen. Bezig met verbinden...');
});
