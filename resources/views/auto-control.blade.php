<x-layout>

    <div class="rollercoaster-interface">

        <!-- VISUELE BAAN -->
        <div class="visual-coaster">
            <div class="station">
                <div class="track">
                    <div class="sensor" id="enterStation"></div>
                    <div class="sensor" id="startPosition"></div>
                    <div class="sensor" id="exitStation"></div>
                </div>
            </div>
            <div class="outside-all">
                <svg class="outside-path" width="100" height="150" viewBox="0 0 100 150">
                    <path d="M 0 0 L 50 0 L 50 150" stroke="black" stroke-width="7" fill="none" />
                </svg>
                <div class="outside-track">
                    <div class="sensor" id="bottomLifthill"></div>
                </div>
                <div class="outside-track">
                    <div class="sensor" id="topLifthill"></div>
                </div>

                <div class="tiltdrop-track">
                    <div class="sensor" id="tiltdropClosed"></div>
                    <div class="sensor" id="tiltdropOpen"></div>
                    <div class="sensor" id="coasterOnTiltdrop"></div>
                </div>

            </div>
        </div>

        <!-- STATUS -->
        <h2>Status</h2>
        <p>Mode: <span id="mode">?</span></p>
        <p>State: <span id="state">?</span></p>

        <!-- DISPATCH -->
        <x-esp-button class="js-esp-button" data-target="dispatch">Dispatch the rollercoaster</x-esp-button>
        <div id="dispatch-status">........</div>
    </div>

</x-layout>

<script>
    /* UPDATE SENSOR + MODE/STATE STATUS */
    async function updateStatus() {
    try {
        // --- Station ESP status ---
        const resStation = await fetch('/auto-control/status');
        const dataStation = await resStation.json();
        document.querySelector("#state").textContent = dataStation.currentState;
        document.querySelector("#mode").textContent = dataStation.manualMode ? "Manual" : "Auto";

        document.querySelector("#exitStation").classList.toggle("active", dataStation.hallSensorExitStation);
        document.querySelector("#bottomLifthill").classList.toggle("active", dataStation.hallSensorBottomLifthill);
        document.querySelector("#topLifthill").classList.toggle("active", dataStation.hallSensorTopLifthill);
        document.querySelector("#enterStation").classList.toggle("active", dataStation.hallSensorEnterStation);
        document.querySelector("#startPosition").classList.toggle("active", dataStation.hallSensorStartPosition);

        // --- TiltDrop ESP status ---
        const resTilt = await fetch('/tiltdrop/status');
        const dataTilt = await resTilt.json();

        document.querySelector("#tiltdropClosed").classList.toggle("active", dataTilt.closed);
        document.querySelector("#tiltdropOpen").classList.toggle("active", dataTilt.open);
        document.querySelector("#coasterOnTiltdrop").classList.toggle("active", dataTilt.coasterOn);

        // --- AUTO TRIGGER TILTDROP ---
        if(dataStation.currentState === 'RIDING' && dataTilt.closed){
            await fetch('/tiltdrop/open', { method: 'POST' });
        }

        if(dataTilt.coasterOn && dataTilt.open){
            await fetch('/tiltdrop/drop', { method: 'POST' });
        }

    } catch(e) {
        console.warn("ESP niet bereikbaar:", e);
    }
}



    /* DISPATCH */
    document.querySelectorAll('.js-esp-button').forEach(btn => {
        btn.addEventListener('click', async () => {
            const target = btn.dataset.target;
            if (target === 'dispatch') {
                await fetch(`/dispatch/go`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                updateDispatchStatus();
            }
        });
    });

    /* DISPATCH STATUS */
    async function updateDispatchStatus() {
        try {
            const response = await fetch('/auto-control/status');
            if (!response.ok) throw new Error('Netwerkfout');

            const data = await response.json();
            const statusDiv = document.getElementById('dispatch-status');

            statusDiv.style.backgroundColor = data.coasterDispatched ? "green" : "orange";
            statusDiv.textContent = data.coasterDispatched ? "Coaster dispatched" : "Coaster in station";

        } catch (error) {
            console.error('Fout bij ophalen status:', error);
            const statusDiv = document.getElementById('dispatch-status');
            statusDiv.textContent = 'Fout bij ophalen status';
            statusDiv.style.backgroundColor = "grey";
        }
    }

    /* INTERVALS */
    setInterval(updateStatus, 500);
    setInterval(updateDispatchStatus, 500);
</script>

<style>
    .rollercoaster-interface {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        padding-top: 50px;
    }

    .visual-coaster {
        position: relative;
        width: 404px;
        height: 175px;
        margin-bottom: 30px;
    }

    .station {
        width: 300px;
        height: 50px;
        border: 2px solid black;
        position: relative;
        z-index: 10;
        background-color: white;
    }

    .track {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
        padding: 0 10px;
        position: relative;
    }

    .track::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 4px;
        background-color: black;
        transform: translateY(-50%);
        z-index: 1;
    }

    .sensor {
        width: 20px;
        height: 20px;
        background-color: gray;
        border-radius: 50%;
        z-index: 2;
        position: relative;
    }

    .sensor.active {
        background-color: limegreen;
    }

    #bottomLifthill {
        margin-top: -100px;
    }

    .outside-all {
        position: absolute;
        left: 304px;
        top: 25px;
        width: 100px;
        height: 200px;
        z-index: 5;
    }

    .outside-path {
        position: absolute;
        top: -2px;
        left: -5px;
        z-index: 1;
    }

    .outside-track {
        position: absolute;
        left: calc(50px - 15px);
        top: calc(150px - 12px);
        z-index: 2;
    }


    #dispatch-status {
        margin-top: 10px;
        padding: 5px 10px;
        font-weight: bold;
        color: white;
        text-align: center;
        background-color: gray;
        min-width: 200px;
    }
</style>
