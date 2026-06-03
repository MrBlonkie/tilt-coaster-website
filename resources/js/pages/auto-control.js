import mqtt from 'mqtt';

let lastStationHeartbeat = null;
let lastTiltdropHeartbeat = null;
let lastBrakesHeartbeat = null;
let lastSwitchtrackHeartbeat = null;
const HEARTBEAT_TIMEOUT = 5000;

let dispatchState = 'stop';
const lastLogCache = {};
const LOG_DEDUP_WINDOW = 1000;

let lastDispatchClick = 0;
let lastEstopClick = 0;
const BUTTON_COOLDOWN = 1000;

const protocol = location.protocol === 'https:' ? 'wss' : 'ws';
const client = mqtt.connect(`${protocol}://${window.MQTT_HOST}:9001`);

client.on('connect', () => {
    client.subscribe([
        'rollercoaster/station/status',
        'rollercoaster/tiltdrop/status',
        'rollercoaster/brakes/status',
        'rollercoaster/switchtrack/status',
        'rollercoaster/event',
        'station/status',
        'tiltdrop/status',
        'brakes/status',
        'switchtrack/status',
        'rollercoaster/block/event',
    ]);

    setConnectStatus('station', 'unknown');
    setConnectStatus('tiltdrop', 'unknown');
    setConnectStatus('brakes', 'unknown');
    setConnectStatus('switchtrack', 'unknown');
});

client.publish('station/manual', 'off');
client.publish('tiltdrop/manual', 'off');
client.publish('brakes/manual', 'off');
client.publish('switchtrack/manual', 'off');

client.on('message', (topic, payload) => {
    const msg = payload.toString().trim();

    if (topic === 'rollercoaster/station/status') {
        if (msg.toLowerCase() === 'online') lastStationHeartbeat = Date.now();
        setConnectStatus('station', 'online');
        return;
    }
    if (topic === 'rollercoaster/tiltdrop/status') {
        if (msg.toLowerCase() === 'online') lastTiltdropHeartbeat = Date.now();
        setConnectStatus('tiltdrop', 'online');
        return;
    }
    if (topic === 'rollercoaster/brakes/status') {
        if (msg.toLowerCase() === 'online') lastBrakesHeartbeat = Date.now();
        setConnectStatus('brakes', 'online');
        return;
    }
    if (topic === 'rollercoaster/switchtrack/status') {
        if (msg.toLowerCase() === 'online') lastSwitchtrackHeartbeat = Date.now();
        setConnectStatus('switchtrack', 'online');
        return;
    }

    if (topic === 'rollercoaster/block/event') {
        addLog(msg, 'blockLogs');
    }

    if (topic === 'rollercoaster/event') {
        addLog(msg, 'eventLogs');

        if (msg.toLowerCase() === 'tiltdrop_opening') animateTiltdrop(true);
        if (msg.toLowerCase() === 'tiltdrop_resetting') animateTiltdrop(false);
    }

    let data;
    try {
        data = JSON.parse(msg);
    } catch (e) {
        return;
    }

    if (topic === 'station/status') updateStationUI(data);
    if (topic === 'tiltdrop/status') updateTiltdropUI(data);
    if (topic === 'brakes/status') updateBrakesUI(data);
    if (topic === 'switchtrack/status') updateSwitchtrackUI(data);
});

setInterval(() => {
    if (lastStationHeartbeat !== null && Date.now() - lastStationHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('station', 'offline');
    if (lastTiltdropHeartbeat !== null && Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('tiltdrop', 'offline');
    if (lastBrakesHeartbeat !== null && Date.now() - lastBrakesHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('brakes', 'offline');
    if (lastSwitchtrackHeartbeat !== null && Date.now() - lastSwitchtrackHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('switchtrack', 'offline');
}, 1000);

function setConnectStatus(device, status) {
    const el = document.getElementById(`${device}-connect-status`);
    const dot = document.getElementById(`${device}-connect-dot`);
    if (!el) return;

    dot?.classList.remove('bg-green-500', 'bg-red-500', 'bg-gray-300', 'animate-pulse');
    el.classList.remove('text-green-600', 'text-red-500', 'text-gray-400');

    if (status === 'online') {
        dot?.classList.add('bg-green-500');
        el.classList.add('text-green-600');
        el.innerText = 'Online';
    } else if (status === 'offline') {
        dot?.classList.add('bg-red-500', 'animate-pulse');
        el.classList.add('text-red-500');
        el.innerText = 'Offline';
    } else {
        dot?.classList.add('bg-gray-300', 'animate-pulse');
        el.classList.add('text-gray-400');
        el.innerText = 'Init...';
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

function updateStationUI(data) {
    if (!data) return;
    const sc = data.blocks.isStationOccupied ? '#ef4444' : '#22c55e';
    const sf = data.blocks.isStationOccupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    const lc = data.blocks.isLifthillOccupied ? '#ef4444' : '#22c55e';
    const lf = data.blocks.isLifthillOccupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    setFill('station-stroke', sc);
    setStroke('station-lifthill-turn', data.cartOnTurn ? '#ef4444' : '#22c55e');
    setFill('lifthill-stroke', lc);
    setStroke('station-block-section', sc);
    setFill('station-block-section', sf);
    setStroke('lifthill-block-section', lc);
    setFill('lifthill-block-section', lf);
}

function updateTiltdropUI(data) {
    if (!data) return;
    const c = data.blocks.isTiltdropOccupied ? '#ef4444' : '#22c55e';
    const f = data.blocks.isTiltdropOccupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    setStroke('layout-stroke', data.trainOnLayout ? '#ef4444' : '#22c55e');
    setStroke('tiltdrop-block-section', c);
    setFill('tiltdrop-block-section', f);
}

function updateBrakesUI(data) {
    if (!data) return;
    const c = data.blocks?.isBrakesOccupied ? '#ef4444' : '#22c55e';
    const f = data.blocks?.isBrakesOccupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    setStroke('brakes-block-section', c);
    setFill('brakes-block-section', f);
}

function updateSwitchtrackUI(data) {
    if (!data) return;
    const c = data.blocks.isSwitchtrackOccupied ? '#ef4444' : '#22c55e';
    const f = data.blocks.isSwitchtrackOccupied ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)';
    setStroke('switchtrack-block-section', c);
    setFill('switchtrack-block-section', f);
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
