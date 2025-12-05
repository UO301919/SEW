class Circuito {

    constructor() {
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
            document.body.innerHTML += "<p>Este navegador no soporta la API File de HTML5.</p>";
        }
    }

    leerArchivoHTML(files) {
        const archivo = files[0];
        const lector = new FileReader();

        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.representarInfoCircuito(contenido);
        };

        lector.readAsText(archivo);
    }

    representarInfoCircuito(contenido) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(contenido, "text/html");
    
        const mainInfo = doc.querySelector("main");
        if (mainInfo) {
            const destino = document.querySelector("#infoCircuito");
            destino.innerHTML += mainInfo.innerHTML;
    
            destino.querySelectorAll("img").forEach(img => {
                if (img.getAttribute("src").startsWith("../multimedia/")) {
                    const nombre = img.getAttribute("src").split("/").pop();
                    img.setAttribute("src", "multimedia/" + nombre);
                }
            });
    
            destino.querySelectorAll("video source").forEach(src => {
                if (src.getAttribute("src").startsWith("../multimedia/")) {
                    const nombre = src.getAttribute("src").split("/").pop();
                    src.setAttribute("src", "multimedia/" + nombre);
                }
            });
        }
    }
     
}

class CargadorSVG {
    constructor(selectorInput, selectorContenedor) {
        this.entrada = document.querySelector(selectorInput);
        this.contenedor = document.querySelector(selectorContenedor);
        this.inicializar();
    }

    inicializar() {
        this.entrada.addEventListener('change', (evento) => this.leerArchivoSVG(evento));
    }

    leerArchivoSVG(evento) {
        const archivo = evento.target.files[0];
        if (archivo && archivo.type === 'image/svg+xml') {
            const lector = new FileReader();
            lector.onload = (e) => this.insertarSVG(e.target.result);
            lector.readAsText(archivo);
        } else {
            alert('Selecciona un archivo SVG válido.');
        }
    }

    insertarSVG(contenidoTexto) {
        const parser = new DOMParser();
        const documentoSVG = parser.parseFromString(contenidoTexto, 'image/svg+xml');
        const elementoSVG = documentoSVG.documentElement;
        this.contenedor.innerHTML = ''; 
        this.contenedor.appendChild(elementoSVG);
    }
}

class CargadorKML {
    #mapa;
    #coordenadas;

    constructor(mapa) {
        this.#mapa = mapa;
        this.#coordenadas = [];
    }

    leerArchivoKML(evento) {
        const archivo = evento.target.files[0];
        if (!archivo) return;

        const lector = new FileReader();
        lector.onload = (e) => {
            const contenido = e.target.result;

            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(contenido, "application/xml");

            const coords = xmlDoc.getElementsByTagName("coordinates");
            this.#coordenadas = [];

            for (let i = 0; i < coords.length; i++) {
                const texto = coords[i].textContent.trim();
                const puntos = texto.trim().split(/\s+/);

                puntos.forEach(p => {
                    const partes = p.split(",");
                    const lng = parseFloat(partes[0]);
                    const lat = parseFloat(partes[1]);
                    this.#coordenadas.push({ lat: lat, lng: lng });
                });
            }

            this.insertarCapaKML();
        };
        lector.readAsText(archivo);
    }

    insertarCapaKML() {
        if (this.#coordenadas.length === 0) return;

        const origen = this.#coordenadas[0];
        new google.maps.Marker({
            position: origen,
            map: this.#mapa,
            title: "Origen del circuito"
        });

        new google.maps.Polyline({
            path: this.#coordenadas,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
            map: this.#mapa
        });

        const bounds = new google.maps.LatLngBounds();
        this.#coordenadas.forEach(coord => bounds.extend(coord));
        this.#mapa.fitBounds(bounds);
    }
}