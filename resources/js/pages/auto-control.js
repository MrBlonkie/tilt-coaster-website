import mqtt from 'mqtt';
import { createHeartbeatManager } from '../heartbeat.js';

let dispatchState = 'stop';
const lastLogCache = {};
const LOG_DEDUP_WINDOW = 1000;

let lastDispatchClick = 0;
let lastEstopClick = 0;
const BUTTON_COOLDOWN = 1000;

const protocol = location.protocol === 'https:' ? 'wss' : 'ws';
const client = mqtt.connect(`${protocol}://${window.MQTT_HOST}:9001`);

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
    device => setConnectStatus(device, 'offline')
);

client.on('connect', () => {
    client.subscribe([
        'rollercoaster/station/status',
        'rollercoaster/tiltdrop/status',
        'rollercoaster/brakes/status',
        'rollercoaster/switchtrack/status',
        'rollercoaster/event',
        'rollercoaster/block/event',
    ]);

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

client.publish('station/manual', 'off');
client.publish('tiltdrop/manual', 'off');
client.publish('brakes/manual', 'off');
client.publish('switchtrack/manual', 'off');

client.on('message', (topic, payload) => {
    const msg = payload.toString().trim();

    if (topic === 'rollercoaster/station/status') {
        if (msg.toLowerCase() === 'online') heartbeat.reset('station');
        return;
    }
    if (topic === 'rollercoaster/tiltdrop/status') {
        if (msg.toLowerCase() === 'online') heartbeat.reset('tiltdrop');
        return;
    }
    if (topic === 'rollercoaster/brakes/status') {
        if (msg.toLowerCase() === 'online') heartbeat.reset('brakes');
        return;
    }
    if (topic === 'rollercoaster/switchtrack/status') {
        if (msg.toLowerCase() === 'online') heartbeat.reset('switchtrack');
        return;
    }

    if (topic === 'rollercoaster/block/event') {
        addLog(msg, 'blockLogs');
        const match = msg.match(/^(\w+)_(occupied|free)$/);
        if (match) applyBlockColor(match[1], match[2] === 'occupied');
        return;
    }

    if (topic === 'rollercoaster/event') {
        addLog(msg, 'eventLogs');
        if (msg.toLowerCase() === 'tiltdrop_opening') animateTiltdrop(true);
        if (msg.toLowerCase() === 'tiltdrop_resetting') animateTiltdrop(false);
    }
});

function setConnectStatus(device, status) {
    const el = document.getElementById(`${device}-connect-status`);
    const monitor = monitorMap[device];
    if (!el) return;

    el.classList.remove('text-emerald-400', 'text-red-400', 'text-gray-400', 'text-gray-500');

    if (status === 'online') {
        el.classList.add('text-emerald-400');
        el.innerText = 'ONLINE';
        monitor?.setColor('#34d399');
    } else if (status === 'offline') {
        el.classList.add('text-red-400');
        el.innerText = 'OFFLINE';
        monitor?.setColor('#f87171');
    } else {
        el.classList.add('text-gray-500');
        el.innerText = 'INIT...';
        monitor?.setColor('#4b5563');
    }
}

const dispatchBtn = document.getElementById('dispatch-button');

dispatchBtn?.addEventListener('click', () => {
    const now = Date.now();
    if (now - lastDispatchClick < BUTTON_COOLDOWN) return;
    lastDispatchClick = now;

    if (dispatchState === 'stop') {
        client.publish('rollercoaster/dispatch', 'go');
        dispatchState = 'go';
    } else {
        client.publish('rollercoaster/dispatch', 'stop');
        dispatchState = 'stop';
    }
    updateDispatchButton();
});

function updateDispatchButton() {
    if (!dispatchBtn) return;
    if (dispatchState === 'go') {
        dispatchBtn.textContent = 'STOP';
        dispatchBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'border-emerald-700');
        dispatchBtn.classList.add('bg-orange-500', 'hover:bg-orange-600', 'border-orange-600');
    } else {
        dispatchBtn.textContent = 'GO';
        dispatchBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600', 'border-orange-600');
        dispatchBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'border-emerald-700');
    }
}

document.querySelectorAll('.clear-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const esp = btn.dataset.esp;
        client.publish(`rollercoaster/clear/${esp}`, 'clear');
        addLog(`CLEAR gestuurd naar ${esp.toUpperCase()}`, 'eventLogs');
    });
});

document.getElementById('estop-button')?.addEventListener('click', () => {
    const now = Date.now();
    if (now - lastEstopClick < BUTTON_COOLDOWN) return;
    lastEstopClick = now;
    client.publish('rollercoaster/estop', 'true');
});

function addLog(message, targetId = 'eventLogs') {
    const now = Date.now();
    const key = `${targetId}:${message}`;

    if (lastLogCache[key] && now - lastLogCache[key] < LOG_DEDUP_WINDOW) return;
    lastLogCache[key] = now;

    const logContainer = document.getElementById(targetId);
    if (!logContainer) return;

    const entry = document.createElement('div');
    const timestamp = new Date().toLocaleTimeString();

    const ts = document.createElement('span');
    ts.className = 'text-gray-500';
    ts.textContent = `[${timestamp}] `;

    const msg = document.createElement('span');
    msg.textContent = message;

    entry.appendChild(ts);
    entry.appendChild(msg);

    logContainer.appendChild(entry);
    logContainer.scrollTop = logContainer.scrollHeight;
}

function setFill(id, color) {
    const el = document.getElementById(id);
    if (el) el.setAttribute('fill', color);
}

function setStroke(id, color) {
    const el = document.getElementById(id);
    if (el) el.setAttribute('stroke', color);
}

const BLOCK_CONFIG = {
    station:     { fills: ['station-stroke'], strokes: ['station-lifthill-turn'],                         strokeFills: ['station-block-section'] },
    lifthill:    { fills: ['lifthill-stroke'],                                                             strokeFills: ['lifthill-block-section'] },
    brakes:      { fills: ['brake-stroke'],                                                                strokeFills: ['brakes-block-section'] },
    layout:      { strokes: ['layout-stroke'] },
    tiltdrop:    { fills: ['tiltdrop-enter-stroke', 'tiltdrop-stroke', 'tiltdrop-exit-stroke', 'tiltdrop-hinge'], strokeFills: ['tiltdrop-block-section'] },
    switchtrack: { fills: ['switch-base-shape', 'switch-stroke'],                                         strokeFills: ['switchtrack-block-section'] },
};

function applyBlockColor(block, occupied) {
    const cfg = BLOCK_CONFIG[block];
    if (!cfg) return;
    const c = occupied ? '#ef4444' : '#22c55e';
    const f = occupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    cfg.fills?.forEach(id => setFill(id, c));
    cfg.strokes?.forEach(id => setStroke(id, c));
    cfg.strokeFills?.forEach(id => { setStroke(id, c); setFill(id, f); });
}

function initMimic() {
    Object.keys(BLOCK_CONFIG).forEach(block => applyBlockColor(block, false));
}

let currentTiltAngle = 0;
let tiltdropAnimating = false;
const hingeX = 769;
const hingeY = 97;
const baseRotation = 'rotate(-90 737.811 97.1158)';

function animateTiltdrop(open = true) {
    const rect = document.getElementById('tiltdrop-stroke');
    if (!rect || tiltdropAnimating) return;

    const targetAngle = open ? 90 : 0;
    if (Math.abs(currentTiltAngle - targetAngle) < 0.1) return;

    tiltdropAnimating = true;
    const speed = 0.5;

    function step() {
        let reachedTarget = false;

        if (currentTiltAngle < targetAngle) {
            currentTiltAngle += speed;
            if (currentTiltAngle >= targetAngle) { currentTiltAngle = targetAngle; reachedTarget = true; }
        } else {
            currentTiltAngle -= speed;
            if (currentTiltAngle <= targetAngle) { currentTiltAngle = targetAngle; reachedTarget = true; }
        }

        rect.setAttribute('transform', `rotate(${currentTiltAngle} ${hingeX} ${hingeY}) ${baseRotation}`);

        if (!reachedTarget) {
            requestAnimationFrame(step);
        } else {
            tiltdropAnimating = false;
        }
    }

    requestAnimationFrame(step);
}

document.addEventListener('DOMContentLoaded', () => {
    monitorMap.station     = new HeartbeatMonitor('station-monitor');
    monitorMap.tiltdrop    = new HeartbeatMonitor('tiltdrop-monitor');
    monitorMap.brakes      = new HeartbeatMonitor('brakes-monitor');
    monitorMap.switchtrack = new HeartbeatMonitor('switchtrack-monitor');

    setConnectStatus('station', 'unknown');
    setConnectStatus('tiltdrop', 'unknown');
    setConnectStatus('brakes', 'unknown');
    setConnectStatus('switchtrack', 'unknown');

    initMimic();
});
