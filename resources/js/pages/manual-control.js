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

function setCardEnabled(device, enabled) {
    const card = document.getElementById(`${device}-card`);
    if (!card) return;
    card.classList.toggle('opacity-50', !enabled);
    card.classList.toggle('pointer-events-none', !enabled);
}

function activateTab(key) {
    document.querySelectorAll('.json-tab').forEach(tab => {
        const isActive = tab.dataset.target === key;
        tab.style.color = isActive ? 'var(--color-ember)' : '';
        tab.style.borderBottom = isActive ? '2px solid var(--color-ember)' : '2px solid transparent';
        tab.classList.toggle('text-gray-500', !isActive);
    });
    document.querySelectorAll('[id$="-json-panel"]').forEach(panel => panel.classList.add('hidden'));
    document.getElementById(`${key}-json-panel`)?.classList.remove('hidden');
}

function setManualControlsEnabled(device, enabled) {
    const controls = document.getElementById(`${device}-motor-controls`);
    if (!controls) return;
    controls.classList.toggle('opacity-40', !enabled);
    controls.classList.toggle('pointer-events-none', !enabled);
}

function setConnectStatus(device, status) {
    currentStatus[`${device}Lwt`] = status;
    const el  = document.getElementById(`${device}-connect-status`);
    const dot = document.getElementById(`${device}-connect-dot`);
    if (!el) return;

    el.classList.remove('text-emerald-400', 'text-red-400', 'text-gray-400');
    dot?.classList.remove('bg-emerald-400', 'bg-red-400', 'bg-gray-300', 'animate-pulse');

    if (status === 'online') {
        el.classList.add('text-emerald-400');
        el.innerText = 'ONLINE';
        dot?.classList.add('bg-emerald-400', 'animate-pulse');
        setCardEnabled(device, true);
    } else if (status === 'offline') {
        el.classList.add('text-red-400');
        el.innerText = 'OFFLINE';
        dot?.classList.add('bg-red-400');
        setCardEnabled(device, false);
    } else {
        el.classList.add('text-gray-400');
        el.innerText = 'INIT...';
        dot?.classList.add('bg-gray-300');
    }
}

const heartbeat = createHeartbeatManager(
    device => setConnectStatus(device, 'online'),
    device => setConnectStatus(device, 'offline')
);

heartbeat.initAll(['station', 'tiltdrop', 'brakes', 'switchtrack']);

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
        { id: 'releasebrakesmotor',      esp: 'brakes',      path: ['motors', 'releaseBrakesMotorState'] },
        { id: 'releaseswitchtrackmotor', esp: 'switchtrack', path: ['switchtrack', 'releaseswitchMotorState'] },
    ];

    map.forEach(m => {
        const el = document.getElementById(`${m.id}-status`);
        if (!el) return;

        el.classList.remove(
            'text-emerald-400', 'text-red-400', 'text-yellow-400', 'text-orange-400', 'text-gray-600',
            'bg-emerald-50', 'bg-red-50', 'bg-yellow-50', 'bg-orange-50', 'bg-gray-100'
        );

        const val = m.path
            ? getNested(currentStatus[m.esp], m.path)
            : currentStatus[m.esp]?.[m.field];

        if (m.type === 'switchtrack') {
            const moving = getNested(currentStatus.switchtrack, ['switchtrack', 'isSwitchtrackMoving']);
            const target = getNested(currentStatus.switchtrack, ['switchtrack', 'manualRotateTarget']);

            if (moving) {
                el.classList.add('text-yellow-400', 'bg-yellow-50');
                el.innerText = 'MOVING';
            } else if (target === 'station') {
                el.classList.add('text-emerald-400', 'bg-emerald-50');
                el.innerText = 'STATION';
            } else if (target === 'brakes') {
                el.classList.add('text-emerald-400', 'bg-emerald-50');
                el.innerText = 'BRAKES';
            } else {
                el.classList.add('text-gray-600', 'bg-gray-100');
                el.innerText = 'UNKNOWN';
            }
            return;
        }

        if (m.type === 'tiltdrop') {
            const moving = getNested(currentStatus.tiltdrop, ['tiltdrop', 'tiltdropMotorMoving']);
            const open   = getNested(currentStatus.tiltdrop, ['tiltdrop', 'isTiltdropTrackOpen']);
            const closed = getNested(currentStatus.tiltdrop, ['sensors', 'hallSensorTiltdropClosedState']);

            if (moving) {
                el.classList.add('text-red-400', 'bg-red-50');
                el.innerText = 'MOVING';
            } else if (open) {
                el.classList.add('text-orange-400', 'bg-orange-50');
                el.innerText = 'OPEN';
            } else if (closed) {
                el.classList.add('text-emerald-400', 'bg-emerald-50');
                el.innerText = 'CLOSED';
            } else {
                el.classList.add('text-gray-600', 'bg-gray-100');
                el.innerText = 'UNKNOWN';
            }
            return;
        }

        if (val === true) {
            el.classList.add('text-emerald-400', 'bg-emerald-50');
            el.innerText = 'ON';
        } else if (val === false) {
            el.classList.add('text-red-400', 'bg-red-50');
            el.innerText = 'OFF';
        } else {
            el.classList.add('text-gray-600', 'bg-gray-100');
            el.innerText = 'UNKNOWN';
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
            const isManual = currentStatus[dev].mode.manualMode;
            toggle.checked = isManual;
            setManualControlsEnabled(dev, isManual);
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

[
    ['manual-switch-station',     'station'],
    ['manual-switch-tiltdrop',    'tiltdrop'],
    ['manual-switch-brakes',      'brakes'],
    ['manual-switch-switchtrack', 'switchtrack'],
].forEach(([id, device]) => {
    const el = document.getElementById(id);
    el?.addEventListener('change', e => {
        client.publish(`${device}/manual`, e.target.checked ? 'on' : 'off');
        setManualControlsEnabled(device, e.target.checked);
        if (e.target.checked) activateTab(device);
    });
});

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

        activateTab(config.esp);

        const finalAction = config.type === 'servo'
            ? (action === 'on' ? 'open' : 'close')
            : action;

        client.publish(config.topic, finalAction);
    });
});

document.querySelectorAll('.json-tab').forEach(tab => {
    tab.addEventListener('click', () => activateTab(tab.dataset.target));
});

document.querySelectorAll('.js-card-header').forEach(header => {
    header.addEventListener('click', e => {
        if (e.target.closest('.toggler')) return;
        activateTab(header.dataset.espKey);
    });
});

document.addEventListener('DOMContentLoaded', updateAllUI);
