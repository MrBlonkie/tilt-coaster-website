import mqtt from 'mqtt';

let lastStationHeartbeat = 0;
let lastTiltdropHeartbeat = 0;
let lastBrakesHeartbeat = 0;
let lastSwitchtrackHeartbeat = 0;
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
    if (Date.now() - lastStationHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('station', 'offline');
    if (Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('tiltdrop', 'offline');
    if (Date.now() - lastBrakesHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('brakes', 'offline');
    if (Date.now() - lastSwitchtrackHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('switchtrack', 'offline');
}, 1000);

function setConnectStatus(device, status) {
    const el = document.getElementById(`${device}-connect-status`);
    if (!el) return;

    el.classList.remove('bg-green-500', 'bg-red-500', 'bg-gray-400');
    if (status === 'online') {
        el.classList.add('bg-green-500');
        el.innerText = 'Online';
    } else if (status === 'offline') {
        el.classList.add('bg-red-500');
        el.innerText = 'Offline';
    } else {
        el.classList.add('bg-gray-400');
        el.innerText = 'Laden...';
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
        dispatchBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        dispatchBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
    } else {
        dispatchBtn.textContent = 'GO';
        dispatchBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
        dispatchBtn.classList.add('bg-green-600', 'hover:bg-green-700');
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
    entry.textContent = `[${timestamp}] ${message}`;

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
    setFill('station-stroke', data.blocks.isStationOccupied ? '#d652f7ff' : '#00FF00');
    setStroke('station-lifthill-turn', data.cartOnTurn ? '#d652f7ff' : '#00FF00');
    setFill('lifthill-stroke', data.blocks.isLifthillOccupied ? '#d652f7ff' : '#00FF00');
    setStroke('station-block-section', data.blocks.isStationOccupied ? '#d652f7ff' : '#00FF00');
    setFill('text-station', data.blocks.isStationOccupied ? '#d652f7ff' : '#00FF00');
    setStroke('lifthill-block-section', data.blocks.isLifthillOccupied ? '#d652f7ff' : '#00FF00');
    setFill('text-lifthill', data.blocks.isLifthillOccupied ? '#d652f7ff' : '#00FF00');
}

function updateTiltdropUI(data) {
    if (!data) return;
    setStroke('layout-stroke', data.trainOnLayout ? '#d652f7ff' : '#00FF00');
    setStroke('tiltdrop-block-section', data.blocks.isTiltdropOccupied ? '#d652f7ff' : '#00FF00');
    setFill('text-tiltdrop', data.blocks.isTiltdropOccupied ? '#d652f7ff' : '#00FF00');
}

function updateBrakesUI(data) {
    if (!data) return;
    setStroke('brakes-block-section', data.blocks?.isBrakesOccupied ? '#d652f7ff' : '#00FF00');
}

function updateSwitchtrackUI(data) {
    if (!data) return;
    setStroke('switchtrack-block-section', data.blocks.isSwitchtrackOccupied ? '#d652f7ff' : '#00FF00');
    setFill('text-switchtrack', data.blocks.isSwitchtrackOccupied ? '#d652f7ff' : '#00FF00');
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
