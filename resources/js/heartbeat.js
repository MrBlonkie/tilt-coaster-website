const HEARTBEAT_TIMEOUT_MS = 5000;

/**
 * @param {(device: string) => void} onOnline  - called when a heartbeat is received
 * @param {(device: string) => void} onOffline - called when the timeout expires
 */
export function createHeartbeatManager(onOnline, onOffline) {
    const timers = {};

    function reset(device) {
        if (timers[device]) clearTimeout(timers[device]);
        onOnline(device);
        timers[device] = setTimeout(() => onOffline(device), HEARTBEAT_TIMEOUT_MS);
    }

    function initAll(devices) {
        devices.forEach(device => {
            timers[device] = setTimeout(() => onOffline(device), HEARTBEAT_TIMEOUT_MS);
        });
    }

    return { reset, initAll };
}
