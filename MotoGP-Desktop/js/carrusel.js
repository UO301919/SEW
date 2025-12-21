class Carrusel {

    #tags;
    #actual;
    #maximo;
    #fotos;
    $img;

    constructor(tags) {
        this.#tags = tags;
        this.#actual = 0;
        this.#maximo = 5;
        this.#fotos = [];
    }

    getFotografias() {
        const flickrAPI = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        
        $.getJSON(flickrAPI, { tags: this.#tags,
            tagmode: "all", 
            format: "json" 
        })
        .done((data) => {
            this.procesarJSONFotografias(data);
            this.mostrarFotografias();
            this.iniciarCarrusel();
        });
    }

    procesarJSONFotografias(datos) {
        const filtradas = datos.items.filter(
            item => /\.(jpg|jpeg|png|webp)$/i.test(item.media.m)
        );
        this.#fotos = filtradas.slice(0, this.#maximo);
    }

    mostrarFotografias() {
        if (this.#fotos.length > 0) {
            const primeraFoto = this.#fotos[0];
    
            const $article = $("<article></article>");
            const $h3 = $("<h3></h3>").text("Imagen del circuito Balaton Park");
    
            this.$img = $("<img>")
                .attr("src", primeraFoto.media.m)
                .attr("alt", "Imagen del circuito Balaton Park");
    
            $article.append($h3).append(this.$img);
    
            const seccionCarrusel = document.querySelector("main > section");
            $(seccionCarrusel).append($article);
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