class Carrusel {

    #busqueda;
    #actual;
    #maximo;
    #fotos;
    $img;

    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#actual = 0;
        this.#maximo = 5;
        this.#fotos = [];
    }

    getFotografias() {
        const flickrAPI = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        
        $.getJSON(flickrAPI, {
            tags: this.#busqueda,
            tagmode: "any",
            format: "json"
        })
        .done((data) => {
            this.procesarJSONFotografias(data);
            this.mostrarFotografias();
            this.iniciarCarrusel();
        });
    }

    procesarJSONFotografias(datos) {
        this.#fotos = datos.items.slice(0, this.#maximo);
    }

    mostrarFotografias() {
        if (this.#fotos.length > 0) {
            const primeraFoto = this.#fotos[0];
    
            const $article = $("<article></article>");
            const $h3 = $("<h3></h3>").text("Imagen del circuito de " + this.#busqueda);
    
            this.$img = $("<img>")
                .attr("src", primeraFoto.media.m)
                .attr("alt", "Imagen del circuito de " + this.#busqueda);
    
            $article.append($h3).append(this.$img);
    
            $("#carrusel").append($article);
        }
    }     

    cambiarFotografia() {
        if (this.#fotos.length > 0) {
            this.#actual++;
            if (this.#actual >= this.#maximo) {
                this.#actual = 0;
            }

            const foto = this.#fotos[this.#actual];
            this.$img.attr("src", foto.media.m);
            this.$img.attr("alt", foto.title);
        }
    }

    iniciarCarrusel() {
        setInterval(this.cambiarFotografia.bind(this), 3000);
    }
}