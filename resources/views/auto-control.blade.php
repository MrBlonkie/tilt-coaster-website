<x-layout>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <div class="max-w-8xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rollercoaster Control Tower</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM 1: VISUELE BAAN (MIMIC PANEL) --}}

            <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Mimic Panel</h2>
                <svg width="910" height="488" viewBox="0 0 910 488" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Group 1">
                        <rect id="station-stroke" x="67" y="95" width="10" height="240" fill="#D9D9D9" />
                        <rect id="lifthill-stroke" x="123" y="52" width="10" height="552"
                            transform="rotate(-90 123 52)" fill="#D9D9D9" />
                        <rect id="brake-stroke" x="129.535" y="373.847" width="10" height="256.439"
                            transform="rotate(-111.427 129.535 373.847)" fill="#D9D9D9" />
                        <circle id="switch-base-shape" cx="64" cy="395" r="29" fill="#D9D9D9" />
                        <rect id="switch-stroke" x="122.636" y="365.531" width="10" height="122.862"
                            transform="rotate(68.5027 122.636 365.531)" fill="#D9D9D9" />
                        <rect id="tiltdrop-enter-stroke" x="682" y="52" width="10" height="74"
                            transform="rotate(-90 682 52)" fill="#D9D9D9" />
                        <rect id="tiltdrop-stroke" x="756.552" y="52.1158" width="10" height="73.8804"
                            transform="rotate(-90 756.552 52.1158)" fill="#D9D9D9" />
                        <rect id="tiltdrop-exit-stroke" x="788" y="82" width="10" height="130" fill="#D9D9D9" />
                        <circle id="tiltdrop-hinge" cx="793" cy="50" r="10" fill="#D9D9D9" />
                        <path id="layout-stroke"
                            d="M792.6 213.41C792.6 224.755 793.285 240.684 793.808 265.682C794.032 276.34 793.99 282.519 793.466 295.804C793.157 303.654 791.916 311.204 791.392 316.923C790.712 324.352 789.157 338.831 788.281 346.474C786.981 357.809 786.046 373.759 785.17 382.247C784.505 388.685 782.935 396.906 781.198 408.852C779.651 419.488 774.991 430.465 772.73 435.494C771.562 438.093 768.426 441.882 764.283 445.683C761.868 447.9 758.398 447.441 754.774 446.58C753.164 446.198 752.176 445.377 751.476 444.511C748.826 441.234 749.397 436.396 749.215 431.04C748.983 424.191 751.118 418.767 752.15 414.613C753.586 408.833 754.561 399.083 755.437 378.208C755.826 368.956 756.988 362.081 757.512 355.854C757.896 351.282 759.746 345.157 760.794 339.432C762.108 332.25 763.21 327.506 764.595 323.353C766.478 317.702 766.321 309.876 767.535 305.552C769.174 299.71 764.952 291.915 763.568 286.714C761.735 279.829 758.398 271.526 755.795 265.802C752.477 258.503 746.659 254.913 740.426 249.899C734.327 244.992 729.361 244.19 723.491 243.313C718.705 242.599 701.827 242.447 674.921 243.474C650.575 244.403 637.063 249.354 633.268 250.733C624.234 254.016 609.789 261.073 601.98 265.594C595.937 269.093 590.085 284.158 586.403 296.167C583.202 306.609 585.19 328.336 587.254 346.116C588.042 352.903 595.54 357.539 607.259 365.136C614.303 369.702 630.022 368.625 646.609 367.936C653.642 367.643 657.068 366.551 667.542 363.295C689.819 356.369 706.152 347.552 710.642 344.446C715.327 341.206 716.19 334.434 717.756 328.714C718.908 324.509 718.97 314.087 718.109 303.146C717.631 297.076 716.211 290.878 713.795 286.019C711.233 280.869 703.123 272.231 695.833 266.315C689.578 261.239 679.624 259.414 672.323 256.8C661.799 253.033 643.306 254.208 620.88 253.684C612.432 253.487 606.304 251.096 599.906 248.68C594.391 246.598 589.712 243.505 585.206 242.276C580.715 241.051 575.877 239.689 570.863 238.133C566.344 236.731 561.027 235.541 552.917 232.269C531.597 223.667 522.656 224.818 506.794 222.235C488.968 219.333 482.564 220.669 468.745 217.055C462.309 215.372 455.6 210.672 449.528 208.059C445.046 206.13 436.27 201.691 427.066 195.282C421.626 191.494 417.209 185.43 413.055 183.17C408.755 180.829 402.711 177.134 392.164 173.152C385.7 170.711 381.265 166.411 376.759 161.92C372.544 157.72 362.287 150.212 358.081 143.943C355.696 140.387 356.355 135.652 356.515 121.346C356.631 111.021 361.519 105.93 364.459 101.424C367.475 96.8018 372.906 91.764 377.765 88.7981C382.056 86.1786 387.777 85.8528 399.304 84.9921C419.973 83.4487 437.545 85.5003 444.276 86.5373C452.708 87.8366 458.297 89.2959 465.043 95.6789C469.155 99.5698 469.725 106.221 469.906 121.056C469.985 127.513 464.913 132.147 461.278 136.995C456.5 143.368 453.153 147.018 451.245 151.171C449.201 155.621 445.894 159.463 444.157 163.616C442.342 167.956 441.393 173.629 440.869 180.027C440.398 185.787 439.319 191.943 438.795 197.834C437.998 206.8 437.245 218.575 437.576 233.394C437.859 246.029 442.409 251.055 445.868 257.635C448.177 262.028 455.497 275.177 462.097 286.138C473.025 304.285 481.464 305.002 487.339 306.739C492.174 308.169 507.971 308.819 517.766 307.787C522.251 307.314 525.02 300.543 529.174 295.015C533.924 288.694 535.744 283.266 539.544 277.728C544.159 271.004 548.541 264.246 550.957 259.408C554.918 251.477 556.153 245.579 558.216 239.881C561.107 231.898 562.022 224.175 562.717 216.723C564.088 202.026 563.765 187.504 563.07 182.49C562.411 177.735 558.248 173.338 553.752 169.864C548.21 165.582 526.597 166.743 512.794 168.635C505.003 169.704 497.062 176.055 489.123 181.93C482.546 186.797 476.341 193.665 471.13 200.255C468.206 203.953 455.663 212.643 447.107 220.306C444.791 222.381 438.665 226.166 428.16 233.404C406.537 248.304 400.616 250.723 396.12 254.358C388.615 260.426 382.986 267.316 377.972 270.266C373.624 272.825 368.467 274.243 364.314 275.799C359.818 278.392 356.012 280.123 353.944 280.984C352.901 281.503 351.875 282.187 350.817 282.892"
                            stroke="#D9D9D9" stroke-width="10" stroke-linecap="round" />
                        <path id="station-lifthill-turn"
                            d="M71.4456 96.6358C71.5908 96.6358 71.6656 93.1512 71.7382 87.6173C71.7836 84.1541 72.3959 81.9845 72.5818 79.4117C72.6999 77.7778 72.9855 75.3144 73.1681 73.5424C73.394 71.3502 73.4255 69.8762 73.6444 68.8478C73.9083 67.6077 74.1559 66.4312 74.5969 65.1091C75.1167 63.551 75.9158 62.7618 76.4668 61.8807C76.9808 61.0591 77.7461 59.9767 78.8482 58.8383C79.6358 58.0248 80.2408 57.1872 81.6696 55.8695C82.5788 55.031 83.5406 54.5474 84.6032 53.9237C86.4073 52.8647 87.7204 52.5674 88.7104 51.9801C90.0996 51.1558 91.2403 51.0979 92.1213 50.879C93.0226 50.6551 94.0253 50.2927 94.9812 49.9991C95.8689 49.7263 96.74 49.4128 97.6574 49.2654C98.8374 49.0758 99.9651 48.7528 100.921 48.6054C102.272 48.3971 104 48.0929 105.282 47.9818C106.432 47.8821 107.3 47.6529 114.389 47.5418C117.267 47.3581 119.909 47.2129 122.218 47.1018C122.701 47.0655 122.919 46.9929 123.583 46.9181"
                            stroke="#D9D9D9" stroke-width="10" stroke-linecap="round" />
                        <rect id="station-block-section" x="3.5" y="116.5" width="88" height="197"
                            stroke="#D9D9D9" stroke-width="3" />
                        <rect id="switchtrack-block-section" x="20.5" y="359.5" width="87" height="127"
                            stroke="#D9D9D9" stroke-width="3" />
                        <rect id="brakes-block-section" x="166.46" y="421.227" width="86.6567" height="219.855"
                            transform="rotate(-111 166.46 421.227)" stroke="#D9D9D9" stroke-width="3" />
                        <rect id="lifthill-block-section" x="140.5" y="1.5" width="523" height="68"
                            stroke="#D9D9D9" stroke-width="3" />
                        <rect id="tiltdrop-block-section" x="688.5" y="1.5" width="220" height="137"
                            stroke="#D9D9D9" stroke-width="3" />

                    </g>
                </svg>





            </div>


            {{-- Connection Card --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Connecties</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Station ESP:</span>
                        <div id="station-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Tiltdrop ESP:</span>
                        <div id="tiltdrop-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Brakes ESP:</span>
                        <div id="brakes-connect-status" class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Switchtrack ESP:</span>
                        <div id="switchtrack-connect-status"
                            class="p-2 w-24 text-center rounded text-white bg-gray-400">
                            Laden...
                        </div>
                    </div>
                </div>
                <div class="">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">ESP Resets</h2>
                    <div class="space-y-2">
                        <button data-esp="station"
                            class="clear-btn w-full bg-yellow-400 hover:bg-yellow-500 font-bold py-2 rounded">
                            Clear Station
                        </button>
                        <button data-esp="lifthill"
                            class="clear-btn w-full bg-yellow-400 hover:bg-yellow-500 font-bold py-2 rounded">
                            Clear Lifthill
                        </button>
                        <button data-esp="tiltdrop"
                            class="clear-btn w-full bg-yellow-400 hover:bg-yellow-500 font-bold py-2 rounded">
                            Clear Tiltdrop
                        </button>
                        <button data-esp="brakes"
                            class="clear-btn w-full bg-yellow-400 hover:bg-yellow-500 font-bold py-2 rounded">
                            Clear Brakes
                        </button>
                        <button data-esp="switchtrack"
                            class="clear-btn w-full bg-yellow-400 hover:bg-yellow-500 font-bold py-2 rounded">
                            Clear Switchtrack
                        </button>
                    </div>
                </div>
            </div>


            {{-- Controls Card --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Controls</h2>
                <div class="space-y-4">
                    <button id="dispatch-button"
                        class="w-full text-white font-bold py-3 px-4 rounded transition duration-300 ease-in-out bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed">DISPATCH</button>
                    <button id="estop-button"
                        class="w-full text-white font-bold py-3 px-4 rounded transition duration-300 ease-in-out bg-red-600 hover:bg-red-700">EMERGENCY
                        STOP</button>


                </div>
            </div>

        </div>

    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        {{-- Kolom 1: Algemene Events --}}
        <div class="bg-black rounded-lg p-4 shadow">
            <h2 class="text-lg font-semibold mb-2 text-blue-400">System Events</h2>
            <div id="eventLogs" class="h-64 overflow-y-auto font-mono text-sm text-blue-400 space-y-1"></div>
        </div>

        {{-- Kolom 2: Block Events --}}
        <div class="bg-black rounded-lg p-4 shadow">
            <h2 class="text-lg font-semibold mb-2 text-green-400">Block Logic Logs</h2>
            <div id="blockLogs" class="h-64 overflow-y-auto font-mono text-sm text-green-400 space-y-1"></div>
        </div>
    </div>

    </div>

    <script>
        // === Heartbeat tracking ===
        let lastStationHeartbeat = 0;
        let lastTiltdropHeartbeat = 0;
        const HEARTBEAT_TIMEOUT = 5000; // 5 seconden

        let dispatchState = 'stop'; // 'go' | 'stop'
        const ESP_DEVICES = ['station', 'tiltdrop', 'brakes', 'switchtrack'];

        const lastLogCache = {};
        const LOG_DEDUP_WINDOW = 1000; // ms


        // === MQTT CLIENT SETUP ===
        const MQTT_HOST = "{{ env('PI_IP') }}";
        const client = mqtt.connect(`ws://${MQTT_HOST}:9001`);

        client.on('connect', () => {
            console.log('MQTT connected!');
            client.subscribe([
                'rollercoaster/station/status',
                'rollercoaster/tiltdrop/status',
                'rollercoaster/brakes/status',
                'rollercoaster/switchtrack/status',
                'rollercoaster/event',
                'station/status',
                'tiltdrop/status',
                'rollercoaster/block/event',
            ]);

            // Initialiseer connectie status
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

            // === LWT / HEARTBEAT ===
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
                if (msg.toLowerCase() === 'online') lastTiltdropHeartbeat = Date.now();
                setConnectStatus('brakes', 'online');
                return;
            }
            if (topic === 'rollercoaster/switchtrack/status') {
                if (msg.toLowerCase() === 'online') lastTiltdropHeartbeat = Date.now();
                setConnectStatus('switchtrack', 'online');
                return;
            }

            if (topic === 'rollercoaster/block/event') {
                addLog(msg, "blockLogs"); // Specifiek voor de rechter kolom
            }

            if (topic === 'rollercoaster/event') {
                addLog(msg, "eventLogs"); // Specifiek voor de linker kolom

                // De animaties blijven reageren op de algemene events
                if (msg.toLowerCase() === 'tiltdrop_opening') animateTiltdrop(true);
                if (msg.toLowerCase() === 'tiltdrop_resetting') animateTiltdrop(false);
            }


            // === STATUS BERICHT (JSON only) ===
            let data;
            try {
                data = JSON.parse(msg);
            } catch (e) {
                // Niet-JSON (bijv. heartbeat strings) negeren
                return;
            }

            if (topic === 'station/status') updateStationUI(data);
            if (topic === 'tiltdrop/status') updateTiltdropUI(data);
            if (topic === 'brakes/status') updateBrakesUI(data);
            if (topic === 'switchtrack/status') updateSwitchtrackUI(data);
        });


        // === Heartbeat interval ===
        setInterval(() => {
            if (Date.now() - lastStationHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('station', 'offline');
            if (Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('tiltdrop', 'offline');
            if (Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('brakes', 'offline');
            if (Date.now() - lastTiltdropHeartbeat > HEARTBEAT_TIMEOUT) setConnectStatus('switchtrack', 'offline');
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

        // === CONTROLS ===
        const dispatchBtn = document.getElementById('dispatch-button');

        dispatchBtn.addEventListener('click', () => {
            if (dispatchState === 'stop') {
                client.publish('rollercoaster/dispatch', 'go');
                dispatchState = 'go';
                updateDispatchButton();
            } else {
                client.publish('rollercoaster/dispatch', 'stop');
                dispatchState = 'stop';
                updateDispatchButton();
            }
        });

        function updateDispatchButton() {
            if (dispatchState === 'go') {
                dispatchBtn.textContent = 'STOP';
                dispatchBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                dispatchBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            } else {
                dispatchBtn.textContent = 'GO';
                dispatchBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                dispatchBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }
        }

        document.querySelectorAll('.clear-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const esp = btn.dataset.esp;
                const topic = `rollercoaster/clear/${esp}`;
                client.publish(topic, 'clear');
                appendLogMessage(`CLEAR gestuurd naar ${esp.toUpperCase()}`);
            });
        });



        document.getElementById('estop-button').addEventListener('click', () => {
            console.log('Publishing: rollercoaster/estop -> true');
            client.publish('rollercoaster/estop', 'true');
        });


        //event log script
        const eventLogs = document.getElementById("eventLogs");

        function addLog(message, targetId = "eventLogs") {
            const now = Date.now();
            const key = `${targetId}:${message}`;

            if (
                lastLogCache[key] &&
                now - lastLogCache[key] < LOG_DEDUP_WINDOW
            ) {
                return; // drop spam
            }

            lastLogCache[key] = now;

            const logContainer = document.getElementById(targetId);
            if (!logContainer) return;

            const entry = document.createElement("div");
            const timestamp = new Date().toLocaleTimeString();
            entry.textContent = `[${timestamp}] ${message}`;

            logContainer.appendChild(entry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }


        // Zet fill-kleur op een SVG shape
        function setFill(id, color) {
            const el = document.getElementById(id);
            if (!el) return;
            el.setAttribute("fill", color);
        }

        // Zet stroke-kleur op een shape (voor paden)
        function setStroke(id, color) {
            const el = document.getElementById(id);
            if (!el) return;
            el.setAttribute("stroke", color);
        }

        // Zet een shape even te knipperen (optioneel)
        function flash(id, color = "yellow", duration = 300) {
            const el = document.getElementById(id);
            if (!el) return;

            const old = el.getAttribute("fill");
            el.setAttribute("fill", color);

            setTimeout(() => {
                el.setAttribute("fill", old);
            }, duration);
        }

        function updateStationUI(data) {
            if (!data) return;

            setFill("station-stroke", data.blocks.isStationOccupied ? "#d652f7ff" : "#00FF00");
            setStroke("station-lifthill-turn", data.cartOnTurn ? "#d652f7ff" : "#00FF00");
            setFill("lifthill-stroke", data.blocks.isLifthillOccupied ? "#d652f7ff" : "#00FF00");

            setStroke("station-block-section", data.blocks.isStationOccupied ? "#d652f7ff" : "#00FF00");
            setFill("text-station", data.blocks.isStationOccupied ? "#d652f7ff" : "#00FF00");

            setStroke("lifthill-block-section", data.blocks.isLifthillOccupied ? "#d652f7ff" : "#00FF00");
            setFill("text-lifthill", data.blocks.isLifthillOccupied ? "#d652f7ff" : "#00FF00");

        }

        function updateTiltdropUI(data) {
            if (!data) return;
            setStroke("layout-stroke", data.trainOnLayout ? "#d652f7ff" : "#00FF00");

            setStroke("tiltdrop-block-section", data.blocks.isTiltdropOccupied ? "#d652f7ff" : "#00FF00");
            setFill("text-tiltdrop", data.blocks.isTiltdropOccupied ? "#d652f7ff" : "#00FF00");
        }

        function updateSwitchtrackUI(data) {
            if (!data) return;
            setStroke("switchtrack-block-section", data.blocks.isSwitchtrackOccupied ? "#d652f7ff" : "#00FF00");
            setFill("text-switchtrack", data.blocks.isSwitchtrackOccupied ? "#d652f7ff" : "#00FF00");
            console.log(data);
        }

        // Huidige hoek van de animatie (0 = vlak/gesloten, 90 = drop/open)
        let currentTiltAngle = 0;
        let tiltdropAnimating = false;

        // Constanten uit je SVG (Hinge punt)
        const hingeX = 769;
        const hingeY = 97;

        // Constanten van de originele positie (uit de SVG <rect> tag)
        const baseRotation = "rotate(-90 737.811 97.1158)";

        function animateTiltdrop(open = true) {
            const rect = document.getElementById("tiltdrop-stroke");
            if (!rect || tiltdropAnimating) return;

            // Bepaal doel: 90 graden (naar beneden) of 0 graden (terug vlak)
            const targetAngle = open ? 90 : 0;

            // Als we er al zijn, doe niets
            if (Math.abs(currentTiltAngle - targetAngle) < 0.1) return;

            tiltdropAnimating = true;
            const speed = .5; // Snelheid van de animatie

            function step() {
                let reachedTarget = false;

                if (currentTiltAngle < targetAngle) {
                    currentTiltAngle += speed;
                    if (currentTiltAngle >= targetAngle) {
                        currentTiltAngle = targetAngle;
                        reachedTarget = true;
                    }
                } else if (currentTiltAngle > targetAngle) {
                    currentTiltAngle -= speed;
                    if (currentTiltAngle <= targetAngle) {
                        currentTiltAngle = targetAngle;
                        reachedTarget = true;
                    }
                }

                // DE FIX: We passen EERST de animatie-rotatie toe rond de Hinge, 
                // en DAARNA pas de basis-rotatie die het blokje horizontaal legt.
                // SVG voert transforms van rechts naar links uit (of binnen naar buiten).
                // Volgorde: Basispositie (-90) -> Dan kantelen (currentAngle)
                rect.setAttribute("transform", `rotate(${currentTiltAngle} ${hingeX} ${hingeY}) ${baseRotation}`);

                if (!reachedTarget) {
                    requestAnimationFrame(step);
                } else {
                    tiltdropAnimating = false;
                }
            }

            requestAnimationFrame(step);
        }
    </script>
</x-layout>
