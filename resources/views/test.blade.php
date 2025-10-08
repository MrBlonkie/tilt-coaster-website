<x-layout>
    <h2>Status</h2>
    <p>Mode: <span id="mode">?</span></p>
    <p>State: <span id="state">?</span></p>

    <h3>Sensoren</h3>
    <div id="exitStation" class="sensor">Exit Station</div>
    <div id="bottomLifthill" class="sensor">Bottom Lifthill</div>
    <div id="enterStation" class="sensor">Enter Station</div>
    <div id="startPosition" class="sensor">Start Position</div>

    <h3>Controls</h3>
    <button onclick="enableManual()">Manual ON</button>
    <button onclick="disableManual()">Manual OFF</button>
    <br><br>
    <button onclick="stationMotorOn()">Stationmotor ON</button>
    <button onclick="stationMotorOff()">Stationmotor OFF</button>
    <br><br>
    <button onclick="lifthillMotorOn()">Lifthillmotor ON</button>
    <button onclick="lifthillMotorOff()">Lifthillmotor OFF</button>
    <br><br>
    <button onclick="dispatchGo()">AUTO Dispatch</button>

    <style>
        .sensor {
            display: inline-block;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 6px;
            background: #777;
            color: white;
        }

        .sensor.active {
            background: limegreen;
        }

        button {
            padding: 8px 12px;
            margin: 4px;
            border: none;
            border-radius: 8px;
            background: #333;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #555;
        }
    </style>

    <script>
        const BASE_URL = "/esp";

        async function updateStatus() {
            try {
                const res = await fetch(`${BASE_URL}/status`);
                if (!res.ok) return;
                const data = await res.json();

                document.querySelector("#state").textContent = data.currentState;
                document.querySelector("#mode").textContent = data.manualMode ? "Manual" : "Auto";

                document.querySelector("#exitStation").classList.toggle("active", data.hallSensorExitStation);
                document.querySelector("#bottomLifthill").classList.toggle("active", data.hallSensorBottomLifthill);
                document.querySelector("#enterStation").classList.toggle("active", data.hallSensorEnterStation);
                document.querySelector("#startPosition").classList.toggle("active", data.hallSensorStartPosition);
            } catch (e) {
                console.warn("ESP niet bereikbaar");
            }
        }

        async function post(path) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            await fetch(`${BASE_URL}${path}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "application/json"
                }
            });
        }


        function enableManual() {
            post("/manual/on");
        }

        function disableManual() {
            post("/manual/off");
        }

        function stationMotorOn() {
            post("/manual/station/on");
        }

        function stationMotorOff() {
            post("/manual/station/off");
        }

        function lifthillMotorOn() {
            post("/manual/lifthill/on");
        }

        function lifthillMotorOff() {
            post("/manual/lifthill/off");
        }

        function dispatchGo() {
            post("/dispatch/go");
        }

        setInterval(updateStatus, 200);
    </script>
</x-layout>
