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
        const filtradas = (datos?.items ?? []).filter(item =>
            item?.media?.m && /\.(jpg|jpeg|png|webp)$/i.test(item.media.m)
        );
    
        this.#fotos = filtradas
            .slice(0, this.#maximo)
            .map(item => {
                const urlM = item.media.m;
                const url640 = urlM.replace("_m.", "_z."); 
    
                const titulo = item.title && item.title.trim() !== ""
                    ? item.title
                    : "Foto de " + this.#tags;
    
                return {
                    src: url640,
                    titulo
                };
            });
    }

    mostrarFotografias() {
        if (this.#fotos.length > 0) {
            const primeraFoto = this.#fotos[0];
    
            const $article = $("<article></article>");
            const $h3 = $("<h3></h3>").text("Imagen del circuito Balaton Park");
    
            this.$img = $("<img>")
                .attr("src", primeraFoto.src)
                .attr("alt", primeraFoto.titulo);
    
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
            this.$img.attr("src", foto.src);
            this.$img.attr("alt", foto.titulo);
        }
    }

    iniciarCarrusel() {
        setInterval(this.cambiarFotografia.bind(this), 3000);
    }
}