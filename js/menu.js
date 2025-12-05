class Menu {
    constructor(selectorHeader = "header") {
      this.header = document.querySelector(selectorHeader);
      this.boton = this.header?.querySelector("button");
      this.nav = this.header?.querySelector("nav");
  
      if (this.boton && this.nav) {
        this.inicializar();
      }
    }
  
    inicializar() {
      this.boton.addEventListener("click", () => this.toggleMenu());
    }
  
    toggleMenu() {
      const abierto = this.nav.classList.toggle("abierto");
      this.boton.setAttribute("aria-expanded", abierto);
    }
  }
  
  // Inicialización automática
  document.addEventListener("DOMContentLoaded", () => {
    new Menu();
  });
  