class Ciudad {

    #nombre;
    #pais;
    #gentilicio;
    #poblacion;
    #coordenadas;

    constructor(nombre, pais, gentilicio) {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
        this.#poblacion = null;
        this.#coordenadas = null;
    }

    rellenaDatos(poblacion, coordenadas) {
        this.#poblacion = poblacion;
        this.#coordenadas = coordenadas;
    }

    getNombreCiudad() {
        return this.#nombre;
    }

    getPais() {
        return this.#pais;
    }

    getInformacionSecundaria() {
        const ul = document.createElement("ul");

        const liGentilicio = document.createElement("li");
        liGentilicio.textContent = `Gentilicio: ${this.#gentilicio}`;
        ul.appendChild(liGentilicio);

        const liPoblacion = document.createElement("li");
        liPoblacion.textContent = `Población: ${this.#poblacion}`;
        ul.appendChild(liPoblacion);

        return ul;
    }

    escribirCoordenadas(contenedor) {
        const pCoord = document.createElement("p");
        pCoord.textContent = `Coordenadas: Latitud ${this.#coordenadas.lat}, Longitud: ${this.#coordenadas.lon}`;
        contenedor.appendChild(pCoord);
    }

    getMeteorologiaCarrera(fechaCarrera) {
        const url = "https://archive-api.open-meteo.com/v1/archive?"
                  + "latitude=" + this.#coordenadas.lat
                  + "&longitude=" + this.#coordenadas.lon
                  + "&start_date=" + fechaCarrera
                  + "&end_date=" + fechaCarrera
                  + "&hourly=temperature_2m,apparent_temperature,precipitation,"
                  + "relative_humidity_2m,windspeed_10m,winddirection_10m"
                  + "&daily=sunrise,sunset"
                  + "&timezone=auto";
    
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: (datos) => {    
                this.procesarJSONCarrera(datos);
            }
        });
    }

    procesarJSONCarrera(datos) {
        let output = "<ul>";
    
        output += "<li>Temperatura (hora 12): " + datos.hourly.temperature_2m[12] + " ºC</li>";
        output += "<li>Sensación térmica (hora 12): " + datos.hourly.apparent_temperature[12] + " ºC</li>";
        output += "<li>Lluvia (hora 12): " + datos.hourly.precipitation[12] + " mm</li>";
        output += "<li>Humedad (hora 12): " + datos.hourly.relative_humidity_2m[12] + " %</li>";
        output += "<li>Viento (hora 12): " + datos.hourly.windspeed_10m[12] + " km/h</li>";
        output += "<li>Dirección del viento (hora 12): " + datos.hourly.winddirection_10m[12] + " º</li>";
    
        const salida = new Date(datos.daily.sunrise[0]).toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit"
        });
        const puesta = new Date(datos.daily.sunset[0]).toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit"
        });
    
        output += "<li>Salida del sol: " + salida + "</li>";
        output += "<li>Puesta del sol: " + puesta + "</li>";
    
        output += "</ul>";
    
        const seccionCarrera = document.querySelector("main > section:nth-of-type(1)")
        seccionCarrera.innerHTML += output;
    }
    

    getMeteorologiaEntrenos(startDate, endDate) {
        const url = "https://archive-api.open-meteo.com/v1/archive?"
                  + "latitude=" + this.#coordenadas.lat
                  + "&longitude=" + this.#coordenadas.lon
                  + "&start_date=" + startDate
                  + "&end_date=" + endDate
                  + "&hourly=temperature_2m,precipitation,relative_humidity_2m,windspeed_10m"
                  + "&timezone=auto";
    
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: (datos) => {
                this.procesarJSONEntrenos(datos);
            }
        });
    }
    
    procesarJSONEntrenos(datos) {
        let output = "<ul>";
    
        const dias = {};
        for (let i = 0; i < datos.hourly.time.length; i++) {
            const fecha = datos.hourly.time[i].split("T")[0];
            if (!dias[fecha]) {
                dias[fecha] = {temp: [], lluvia: [], humedad: [], viento: []};
            }
            dias[fecha].temp.push(datos.hourly.temperature_2m[i]);
            dias[fecha].lluvia.push(datos.hourly.precipitation[i]);
            dias[fecha].humedad.push(datos.hourly.relative_humidity_2m[i]);
            dias[fecha].viento.push(datos.hourly.windspeed_10m[i]);
        }
    
        for (const fecha in dias) {
            const d = dias[fecha];
            const media = arr => (arr.reduce((a,b)=>a+b,0)/arr.length).toFixed(2);
            output += `<li>${fecha} → 
                Temp: ${media(d.temp)} ºC, 
                Lluvia: ${media(d.lluvia)} mm, 
                Humedad: ${media(d.humedad)} %, 
                Viento: ${media(d.viento)} km/h</li>`;
        }
    
        output += "</ul>";
    
        const seccionCarrera = document.querySelector("main > section:nth-of-type(2)")
        seccionCarrera.innerHTML += output;
    }
    
        
}
