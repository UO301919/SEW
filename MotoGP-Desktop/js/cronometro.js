class Cronometro {

    #tiempo;
    #inicio;
    #corriendo;
    #cronometro;

    constructor() {
        this.#tiempo = 0;
        this.#inicio = null;
        this.#corriendo = null;
        this.#cronometro = document.querySelector("main p");
        this.#mostrar();
    }

    inicializarEventos() {
        const [btnArrancar, btnParar, btnReiniciar] = document.querySelectorAll("main button");

        btnArrancar.addEventListener("click", this.arrancar.bind(this));
        btnParar.addEventListener("click", this.parar.bind(this));
        btnReiniciar.addEventListener("click", this.reiniciar.bind(this));
    }

    arrancar() {
        if(this.#corriendo) return;
        this.#inicio = performance.now() - this.#tiempo;
        this.#corriendo = setInterval(() => {
            this.#actualizar();
            this.#mostrar();
        }, 100);
    }

    #actualizar() {
        const ahora = performance.now();
        this.#tiempo = ahora - this.#inicio;
    }

    #mostrar() {
        const minutos = parseInt(this.#tiempo / 60000);
        const segundos = parseInt((this.#tiempo % 60000) / 1000);
        const decimas = parseInt((this.#tiempo % 1000) / 100);

        const formatoMin = String(minutos).padStart(2, '0');
        const formatoSeg = String(segundos).padStart(2, '0');

        const texto = `${formatoMin}:${formatoSeg}.${decimas}`;

        if (this.#cronometro) {
            this.#cronometro.textContent = texto;
        }
    }

    parar() {
        if (this.#corriendo) {
            clearInterval(this.#corriendo);
            this.#corriendo = null;
        }
    }

    reiniciar() {
        this.parar();
        this.#tiempo = 0;
        this.#mostrar();
    }
}
