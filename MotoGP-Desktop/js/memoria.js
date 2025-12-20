class Memoria {

    #tablero_bloqueado;
    #primera_carta;
    #segunda_carta;
    #cronometro;

    constructor() {
        this.#tablero_bloqueado = true;
        this.#primera_carta = null;
        this.#segunda_carta = null;

        this.#barajarCartas();
        this.#tablero_bloqueado = false;

        this.#inicializarEventos();

        this.#cronometro = new Cronometro();
        this.#cronometro.arrancar();
    }

    inicializarEventos() {
        this.#inicializarEventos();
    }

    #inicializarEventos() {
        const cartas = document.querySelectorAll("main article");
        cartas.forEach(carta => {
            carta.addEventListener("click", () => {
                this.#voltearCarta(carta);
            });
        });
    }

    #voltearCarta(carta) {
        if (
            carta.dataset.estado === "volteada" ||
            carta.dataset.estado === "revelada" ||
            this.#tablero_bloqueado
        ) return;

        carta.dataset.estado = "volteada";

        if (!this.#primera_carta) {
            this.#primera_carta = carta;
        } else {
            this.#segunda_carta = carta;
            this.#comprobarPareja();
        }
    }

    #barajarCartas() {
        const contenedor = document.querySelector("main");
        const cartas = Array.from(contenedor.querySelectorAll("article"));

        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        cartas.forEach(carta => contenedor.appendChild(carta));
    }

    #reiniciarAtributos() {
        this.#tablero_bloqueado = false;
        this.#primera_carta = null;
        this.#segunda_carta = null;
    }

    #deshabilitarCartas() {
        this.#primera_carta.dataset.estado = "revelada";
        this.#segunda_carta.dataset.estado = "revelada";
        this.#comprobarJuego();
        this.#reiniciarAtributos();
    }

    #comprobarJuego() {
        const cartas = document.querySelectorAll("main article");
        const todasReveladas = Array.from(cartas).every(
            carta => carta.dataset.estado === "revelada"
        );
        if (todasReveladas) {
            this.#cronometro.parar();
        }
    }

    #cubrirCartas() {
        this.#tablero_bloqueado = true;
        setTimeout(() => {
            this.#primera_carta.removeAttribute("data-estado");
            this.#segunda_carta.removeAttribute("data-estado");
            this.#reiniciarAtributos();
        }, 1500);
    }

    #comprobarPareja() {
        const imgPrimera = this.#primera_carta.children[1].src;
        const imgSegunda = this.#segunda_carta.children[1].src;

        if (imgPrimera === imgSegunda) {
            this.#deshabilitarCartas();
        } else {
            this.#cubrirCartas();
        }
    }
}

window.addEventListener("DOMContentLoaded", () => {
    new Memoria();
});

