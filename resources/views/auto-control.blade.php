<x-layout>

    <div class="visual-coaster">
        <div class="station">
            <div class="track">
                <div class="sensor" id="sensor1"></div>
                <div class="sensor" id="sensor2"></div>
                <div class="sensor" id="sensor3"></div>
            </div>
        </div>
        <div class="outside-all">
            <svg class="outside-path" width="100" height="150" viewBox="0 0 100 150">
            <path d="M 0 0 L 50 0 L 50 150" stroke="black" stroke-width="4" fill="none"/>
        </svg>

        <div class="outside-track">
            <div class="sensor" id="sensor4"></div>
        </div>
        </div>
        
    </div>

</x-layout>

<style>
    <style>
    /* Zorg voor een positioneer-context voor de absolute elementen */
    .visual-coaster {
        margin-top: 100px;
        margin-left: 100px;
        /* Zorgt ervoor dat de outside-path en outside-track goed uitlijnen t.o.v. de container */
    }

    /* --- Het Station --- */
    .station {
        width: 300px;
        height: 50px; /* Kleinere hoogte is genoeg, zodat de baanlijn centraal staat */
        border: 2px solid black;
        position: relative;
        margin-bottom: 50px;
    }

    /* De Horizontale Baanlijn Binnen het Station */
    .track {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
        padding: 0 10px;
        position: relative; /* Noodzakelijk voor de positie van het pseudo-element */
    }

    /* Tekenen van de rechte lijn binnen het station */
    .track::before {
        content: "";
        position: absolute;
        top: 50%; /* Centreer de lijn verticaal */
        left: 0;
        right: 0;
        height: 4px; /* Dikte van de lijn */
        background-color: black;
        transform: translateY(-50%); /* Fijnere verticale centrering */
        z-index: 1; /* Zorgt ervoor dat de lijn onder de sensoren ligt */
    }

    /* --- De Sensoren (Bollen) --- */
    .sensor {
        width: 20px;
        height: 20px;
        background-color: gray;
        /* kan je later groen/rood maken afhankelijk van hall sensor */
        border-radius: 50%;
        z-index: 2; /* Zorgt ervoor dat de sensor boven de lijn ligt */
        position: relative; /* Zorgt ervoor dat z-index werkt */
    }

    /* --- De Buitenbaan (Bocht en Sensor 4) --- */

    .outside-all {
        position: absolute;
        /* Start vanaf het einde van het station (300px) */
        left: 300px; 
        /* Start op het midden van de baan (50px / 2 = 25px) */
        top: 88px; 
        /* Geef het voldoende ruimte */
        width: 100px; 
        height: 200px;
    }

    /* --- De Bocht en Sensor 4 (Relatief aan .outside-all) --- */

    /* De positie van de SVG (de bocht) is nu RELATIEF t.o.v. .outside-all */
    .outside-path {
        position: absolute;
        /* Start de lijn vanaf (0, 0) binnen de outside-all container */
        top: 0; 
        left: 0; 
        z-index: 1;
    }

    /* De sensor buiten het station is nu RELATIEF t.o.v. .outside-all */
    .outside-track {
        position: absolute;
        /* Positioneer de div onderaan de verticale lijn van de SVG */
        /* Horizontaal: Halve breedte van de bochtlijn (50px) min halve sensorbreedte (10px) */
        left: calc(50px - 10px); 
        /* Verticaal: Hoogte SVG (150px) min halve sensorhoogte (10px) */
        top: calc(150px - 10px); 
        z-index: 2;
    }
    
    /* Zorg dat sensor 4 netjes in de outside-track div zit (deze div is nu vooral een container voor de positie) */
    #sensor4 {
        /* Je kan deze stijlen verwijderen omdat .sensor ze al heeft, maar voor duidelijkheid laat ik ze staan */
        width: 20px;
        height: 20px;
        background-color: gray;
        border-radius: 50%;
    }

</style>
