class Noticias {

    #busqueda;
    #url;
    #apiKey;

    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#url = "https://api.thenewsapi.com/v1/news/all";
        this.#apiKey = "D81XAfOhdsT9BQLEj4vz2KGbGjGKZuPfHzsKLdre";
    }

    buscar() {
        const endpoint = `${this.#url}?api_token=${this.#apiKey}&search=${this.#busqueda}&language=es&limit=5`;

        return fetch(endpoint)
            .then(response => {
                return response.json(); 
            });
    }

    procesarInformacion(datos) {
        const contenedor = document.querySelectorAll("main > section")[1];

    
        datos.data.forEach(noticia => {
            const articulo = $("<article></article>");
            const titular = $("<h3></h3>").text(noticia.title);
            articulo.append(titular);
            const entradilla = $("<p></p>").text(noticia.description);
            articulo.append(entradilla);
            const enlace = $("<a></a>")
                .attr("href", noticia.url)
                .attr("target", "_blank")
                .text("Leer más");
            articulo.append(enlace);
            const fuente = $("<p></p>").text("Fuente: " + noticia.source);
            articulo.append(fuente);
            $(contenedor).append(articulo);

        });
    }    
    
}
