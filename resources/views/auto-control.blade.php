<x-layout>

    <div class="rollercoaster-interface">

        <div class="visual-coaster">
            <div class="station">
                <div class="track">
                    <div class="sensor" id="hallSensorEnterStation"></div>
                    <div class="sensor" id="hallSensorStartPosition"></div>
                    <div class="sensor" id="hallSensorExitStation"></div>
                </div>
            </div>
            <div class="outside-all">
                <svg class="outside-path" width="100" height="150" viewBox="0 0 100 150">
                    <path d="M 0 0 L 50 0 L 50 150" stroke="black" stroke-width="7" fill="none" />
                </svg>
                <div class="outside-track">
                    <div class="sensor" id="hallSensorBottomLifthill"></div>
                </div>
            </div>
        </div>
        
        <x-esp-button data-target="dispatch">Dispatch the rollercoaster</x-esp-button>
        <div id="dispatch-status">........</div>

    </div>

</x-layout>



<script>
document.querySelectorAll('.js-esp-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.target;
        const action = btn.dataset.action;

        switch(target){
            case 'dispatch':
                dispatchCoaster();
                break;
        }
    });
});

// Dispatch
async function dispatchCoaster(){
    await fetch(`/dispatch/go`, {
        method:'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    updateDispatchStatus();
}

async function updateDispatchStatus() {
    try {
        const response = await fetch('/auto-control/status'); 
        if (!response.ok) throw new Error('Netwerkfout');

        const data = await response.json();
        const statusDiv = document.getElementById('dispatch-status');

        statusDiv.style.backgroundColor = data.coasterDispatched ? "green" : "red";
        statusDiv.textContent = data.coasterDispatched ? "coaster is dispatched" : "coaster in het station";

    } catch (error) {
        console.error('Fout bij ophalen status:', error);
        document.getElementById('dispatch-status').textContent = 'Fout bij ophalen status';
        document.getElementById('dispatch-status').style.backgroundColor = "grey";
    }
}

async function updateSensors() {
    try {
        const response = await fetch('/auto-control/status');
        if (!response.ok) throw new Error('Netwerkfout');
        const data = await response.json();

        const sensorIds = [
            'hallSensorEnterStation',
            'hallSensorStartPosition',
            'hallSensorExitStation',
            'hallSensorBottomLifthill'
        ];

        sensorIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.backgroundColor = data[id] ? 'green' : 'red';
            }
        });

    } catch (error) {
        console.error('Fout bij ophalen sensorstatus:', error);
    }
}


const updateInterval = setInterval(updateSensors, 2000);

window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
});


</script>



<style>
/* NIEUWE WRAPPER OM ALLES TE CENTREREN */
.rollercoaster-interface {
    /* Gebruik Flexbox om de inhoud te centreren */
    display: flex;
    flex-direction: column; /* Stapel de elementen verticaal */
    align-items: center; /* Centreer horizontaal */
    width: 100%; /* Neem de volledige breedte in beslag */
    padding-top: 50px; /* Wat ruimte bovenaan */
}

/* VISUELE ELEMENTEN CENTREREN BINNEN DE WRAPPER */
.visual-coaster {
    /* Verwijder de vaste marges die centrering verhinderden */
    margin-top: 0; 
    margin-left: 0;
    
    /* CRUCIAAL: Maakt dit de ankerplaats voor absolute kinderen */
    position: relative;
    
    /* Bepaal de totale breedte zodat de wrapper correct kan centreren */
    width: 404px; /* 304px (station) + 100px (outside-all) */
    
    /* De totale hoogte wordt bepaald door het hoogste element in flow. 
       We moeten voldoende ruimte overlaten voor de outside-all baan (150px) 
       die absoluut is. */
    height: 175px; /* Geef de container voldoende hoogte om de buitenbaan te bevatten. 
                      Station (50px) + offset (25px) + buitenbaanhoogte (150px) is ~225px.
                      175px is een veilig minimum. */
    
    /* Ruimte onder de visualisatie zodat de knop eronder komt */
    margin-bottom: 30px; 
}

/* --- Het Station --- */
.station {
    width: 300px;
    height: 50px;
    border: 2px solid black;
    /* Terug op relative, zodat het in de flow van .visual-coaster blijft */
    position: relative; 
    /* Zorg dat het station bovenaan begint, is de referentie voor de baan */
    top: 0;
    margin-bottom: 0; 
    /* Zorg dat de knop en status er NIET onder komen te staan */
    z-index: 10;
    background-color: white; /* Voorkomt overlap van tekst/knop als de z-index niet werkt */
}

/* De Horizontale Baanlijn Binnen het Station */
.track {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
    padding: 0 10px;
    position: relative;
}

/* Tekenen van de rechte lijn binnen het station */
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

/* --- De Sensoren (Bollen) --- */
.sensor {
    width: 20px;
    height: 20px;
    background-color: gray;
    border-radius: 50%;
    z-index: 2;
    position: relative;
}

/* --- De Buitenbaan (Bocht en Sensor 4) --- */

.outside-all {
    position: absolute;
    /* Start direct na het station (300px breedte + 2x2px rand = 304px) */
    left: 304px; 
    /* Lijn de bovenkant van deze container uit met het midden van het station (50px / 2 = 25px) */
    top: 25px; 
    width: 100px;
    height: 200px;
    z-index: 5; /* Zorgt dat de baan boven het station blijft, maar onder de knoppen/status */
}

/* --- De Bocht en Sensor 4 (Relatief aan .outside-all) --- */
.outside-path {
    position: absolute;
    top: -2;
    left: -5;
    z-index: 1;
}

/* De sensor buiten het station is nu RELATIEF t.o.v. .outside-all */
.outside-track {
    position: absolute;
    left: calc(50px - 15px);
    top: calc(150px - 12px);
    z-index: 2;
}

/* --- KNOP EN STATUS FIXES --- */

/* Maak de custom component knop tot een blok element */
x-esp-button {
    display: block; 
    /* Eventueel centrering in de knop zelf */
    text-align: center;
    margin-top: 10px; /* Extra ruimte boven de knop */
    padding: 10px 20px;
    cursor: pointer;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f0f0f0;
}

/* Styling voor een nette weergave van de status */
#dispatch-status {
    margin-top: 10px;
    padding: 5px 10px;
    font-weight: bold;
    color: white;
    text-align: center;
    /* Zorgt ervoor dat de tekst altijd leesbaar is, ook al is de kleur nog niet gezet door JS */
    background-color: gray; 
    min-width: 200px; /* Geeft het element een minimum grootte */
}
</style>