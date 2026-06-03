import mqtt from 'mqtt';
import { createHeartbeatManager } from '../heartbeat.js';

const protocol = location.protocol === 'https:' ? 'wss' : 'ws';
const client = mqtt.connect(`${protocol}://${window.MQTT_HOST}:9001`);

const currentStatus = {
    station: {},
    tiltdrop: {},
    brakes: {},
    switchtrack: {},
    stationLwt: 'unknown',
    tiltdropLwt: 'unknown',
    brakesLwt: 'unknown',
    switchtrackLwt: 'unknown',
};

function setConnectStatus(device, status) {
    currentStatus[`${device}Lwt`] = status;
    const el = document.getElementById(`${device}-connect-status`);
    if (!el) return;

    el.classList.remove('text-emerald-400', 'text-red-400', 'text-gray-600');
    if (status === 'online') {
        el.classList.add('text-emerald-400');
        el.innerText = 'ONLINE';
    } else if (status === 'offline') {
        el.classList.add('text-red-400');
        el.innerText = 'OFFLINE';
    } else {
        el.classList.add('text-gray-600');
        el.innerText = 'INIT...';
    }
}

const heartbeat = createHeartbeatManager(
    device => setConnectStatus(device, 'online'),
    device => setConnectStatus(device, 'offline')
);

function updateJsonUI() {
    document.getElementById('station-json-output').innerText    = JSON.stringify(currentStatus.station, null, 2);
    document.getElementById('tiltdrop-json-output').innerText   = JSON.stringify(currentStatus.tiltdrop, null, 2);
    document.getElementById('brakes-json-output').innerText     = JSON.stringify(currentStatus.brakes, null, 2);
    document.getElementById('switchtrack-json-output').innerText= JSON.stringify(currentStatus.switchtrack, null, 2);
}

function getNested(obj, path) {
    return path.reduce((o, k) => (o && o[k] !== undefined) ? o[k] : undefined, obj);
}

function updateMotorUI() {
    const map = [
        { id: 'stationmotor',            esp: 'station',     path: ['motors', 'station', 'stationStepperState'] },
        { id: 'lifthillmotor',           esp: 'station',     path: ['motors', 'lift', 'liftStepperState'] },
        { id: 'stationgatesmotor',       esp: 'station',     path: ['gates', 'gatesMotorState'] },
        { id: 'tiltdropmotor',           esp: 'tiltdrop',    type: 'tiltdrop' },
        { id: 'releasedropmotor',        esp: 'tiltdrop',    path: ['tiltdrop', 'releasedropMotorState'] },
        { id: 'switchtrackmotor',        esp: 'switchtrack', field: 'rotateTarget', type: 'switchtrack' },
        { id: 'releasebrakesmotor',      esp: 'brakes',      path: ['brakes', 'releasebrakesMotorState'] },
        { id: 'releaseswitchtrackmotor', esp: 'switchtrack', path: ['switchtrack', 'releaseswitchMotorState'] },
    ];

    map.forEach(m => {
        const el = document.getElementById(`${m.id}-status`);
        if (!el) return;

        el.classList.remove('text-emerald-400', 'text-red-400', 'text-yellow-400', 'text-orange-400', 'text-gray-600');

        const val = m.path
            ? getNested(currentStatus[m.esp], m.path)
            : currentStatus[m.esp]?.[m.field];

        if (m.type === 'switchtrack') {
            const moving = getNested(currentStatus.switchtrack, ['switchtrack', 'isSwitchtrackMoving']);
            const target = getNested(currentStatus.switchtrack, ['switchtrack', 'manualRotateTarget']);

            if (moving) {
                el.classList.add('text-yellow-400');
                el.innerText = 'MOVING';
            } else if (target === 'station') {
                el.classList.add('text-emerald-400');
                el.innerText = 'STATION';
            } else if (target === 'brakes') {
                el.classList.add('text-emerald-400');
                el.innerText = 'BRAKES';
            } else {
                el.classList.add('text-gray-600');
                el.innerText = 'UNKNOWN';
            }
            return;
        }

        if (m.type === 'tiltdrop') {
            const moving = getNested(currentStatus.tiltdrop, ['tiltdrop', 'tiltdropMotorMoving']);
            const open   = getNested(currentStatus.tiltdrop, ['tiltdrop', 'isTiltdropTrackOpen']);
            const closed = getNested(currentStatus.tiltdrop, ['sensors', 'hallSensorTiltdropClosedState']);

            if (moving) {
                el.classList.add('text-red-400');
                el.innerText = 'MOVING';
            } else if (open) {
                el.classList.add('text-orange-400');
                el.innerText = 'OPEN';
            } else if (closed) {
                el.classList.add('text-emerald-400');
                el.innerText = 'CLOSED';
            } else {
                el.classList.add('text-gray-600');
                el.innerText = 'UNKNOWN';
            }
            return;
        }

        if (val === true) {
            el.classList.add('text-emerald-400');
            el.innerText = 'ON';
        } else if (val === false) {
            el.classList.add('text-red-400');
            el.innerText = 'OFF';
        } else {
            el.classList.add('text-gray-600');
            el.innerText = 'LADEN...';
        }
    });
}

function updateAllUI() {
    setConnectStatus('station',     currentStatus.stationLwt);
    setConnectStatus('tiltdrop',    currentStatus.tiltdropLwt);
    setConnectStatus('brakes',      currentStatus.brakesLwt);
    setConnectStatus('switchtrack', currentStatus.switchtrackLwt);

    updateMotorUI();
    updateJsonUI();

    ['station', 'tiltdrop', 'brakes', 'switchtrack'].forEach(dev => {
        const toggle = document.getElementById(`manual-switch-${dev}`);
        if (toggle && typeof currentStatus[dev]?.mode?.manualMode !== 'undefined') {
            toggle.checked = currentStatus[dev].mode.manualMode;
        }
    });
}

client.on('connect', () => {
    client.subscribe('rollercoaster/station/status');
    client.subscribe('rollercoaster/tiltdrop/status');
    client.subscribe('rollercoaster/brakes/status');
    client.subscribe('rollercoaster/switchtrack/status');

    client.subscribe('station/status');
    client.subscribe('tiltdrop/status');
    client.subscribe('brakes/status');
    client.subscribe('switchtrack/status');

    heartbeat.reset('station');
    heartbeat.reset('tiltdrop');
    heartbeat.reset('brakes');
    heartbeat.reset('switchtrack');
});

client.on('message', (topic, payload) => {
    const msg = payload.toString().trim();

    if (topic === 'rollercoaster/station/status'     && msg === 'online') return heartbeat.reset('station');
    if (topic === 'rollercoaster/tiltdrop/status'    && msg === 'online') return heartbeat.reset('tiltdrop');
    if (topic === 'rollercoaster/brakes/status'      && msg === 'online') return heartbeat.reset('brakes');
    if (topic === 'rollercoaster/switchtrack/status' && msg === 'online') return heartbeat.reset('switchtrack');

    try {
        const data = JSON.parse(msg);

        if (topic === 'station/status')     currentStatus.station     = data;
        if (topic === 'tiltdrop/status')    currentStatus.tiltdrop    = data;
        if (topic === 'brakes/status')      currentStatus.brakes      = data;
        if (topic === 'switchtrack/status') currentStatus.switchtrack = data;

        updateAllUI();
    } catch {}
});

document.getElementById('manual-switch-station')?.addEventListener('change', e =>
    client.publish('station/manual', e.target.checked ? 'on' : 'off')
);
document.getElementById('manual-switch-tiltdrop')?.addEventListener('change', e =>
    client.publish('tiltdrop/manual', e.target.checked ? 'on' : 'off')
);
document.getElementById('manual-switch-brakes')?.addEventListener('change', e =>
    client.publish('brakes/manual', e.target.checked ? 'on' : 'off')
);
document.getElementById('manual-switch-switchtrack')?.addEventListener('change', e =>
    client.publish('switchtrack/manual', e.target.checked ? 'on' : 'off')
);

const MOTOR_MAP = {
    stationmotor:            { esp: 'station',     topic: 'station/stationmotor' },
    lifthillmotor:           { esp: 'station',     topic: 'station/lifthillmotor' },
    stationfan:              { esp: 'station',     topic: 'station/stationfan' },
    stationgatesmotor:       { esp: 'station',     topic: 'station/gatesmotor',                   type: 'servo' },
    tiltdropmotor:           { esp: 'tiltdrop',    topic: 'tiltdrop/tiltdropmotor',               type: 'servo' },
    releasedropmotor:        { esp: 'tiltdrop',    topic: 'tiltdrop/releasedropmotor',            type: 'servo' },
    releasebrakesmotor:      { esp: 'brakes',      topic: 'brakes/releasebrakesmotor',            type: 'servo' },
    switchtrackmotor:        { esp: 'switchtrack', topic: 'switchtrack/rotatemotor' },
    releaseswitchtrackmotor: { esp: 'switchtrack', topic: 'switchtrack/releaseswitchtrackmotor',  type: 'servo' },
};

document.querySelectorAll('.js-esp-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.target;
        const action = btn.dataset.action;
        const config = MOTOR_MAP[target];
        if (!config) return;

        const finalAction = config.type === 'servo'
            ? (action === 'on' ? 'open' : 'close')
            : action;

        client.publish(config.topic, finalAction);
    });
});

document.addEventListener('DOMContentLoaded', updateAllUI);
