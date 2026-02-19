/**
 * Clase Paginador Universal Inteligente
 * Soporta modo 'auto' para ajuste vertical responsive
 */
class PaginadorTabla {
  constructor(data, itemsPorPagina, config) {
    this.data = data;
    this.paginaActual = 1;
    this.config = config;
    this.containerId = config.containerId || "tableContainer"; // ID del contenedor padre

    // Elementos DOM (Busca dentro del contenedor si es posible, o globalmente)
    this.tbody = document.getElementById(config.tableBodyId);
    const container = document.getElementById(this.containerId);

    // Selectores universales (funcionan con el table_base.php)
    this.btnPrev = container
      ? container.querySelector(".btn-prev")
      : document.getElementById("btnPrev");
    this.btnNext = container
      ? container.querySelector(".btn-next")
      : document.getElementById("btnNext");
    this.infoText = container
      ? container.querySelector(".pagination-info")
      : document.getElementById("paginationInfo");

    // Configuración de items
    this.isAuto = itemsPorPagina === "auto";
    this.manualItems = typeof itemsPorPagina === "number" ? itemsPorPagina : 5;
    this.itemsPorPagina = this.isAuto
      ? this.calcularItemsAuto()
      : this.manualItems;

    this.init();
  }

  init() {
    if (this.btnPrev) this.btnPrev.addEventListener("click", () => this.prev());
    if (this.btnNext) this.btnNext.addEventListener("click", () => this.next());

    // Si es automático, activamos el sensor de tamaño
    if (this.isAuto) {
      let resizeTimer;
      window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          this.recalcularYRenderizar();
        }, 200);
      });
    }

    this.render();
  }

  calcularItemsAuto() {
    const container = document.getElementById(this.containerId);
    if (!container) return 5;

    // Medidas estándar de tu diseño (header ~60px, footer ~80px, row ~76px)
    const headerHeight = 60;
    const footerHeight = 80;
    const rowHeight = 76;

    const availableHeight =
      container.clientHeight - headerHeight - footerHeight;
    const items = Math.floor(availableHeight / rowHeight);

    return items > 2 ? items : 2; // Mínimo 2 filas
  }

  recalcularYRenderizar() {
    if (!this.isAuto) return;
    const nuevosItems = this.calcularItemsAuto();

    // Solo renderizar si cambió la cantidad para no parpadear
    if (nuevosItems !== this.itemsPorPagina) {
      this.itemsPorPagina = nuevosItems;
      // Ajustar página actual si nos salimos del rango
      const totalPaginas = Math.ceil(this.data.length / this.itemsPorPagina);
      if (this.paginaActual > totalPaginas)
        this.paginaActual = totalPaginas || 1;
      this.render();
    }
  }

  setData(newData) {
    this.data = newData;
    this.paginaActual = 1;
    this.render();
  }

  getTotalPaginas() {
    return Math.ceil(this.data.length / this.itemsPorPagina);
  }

  render() {
    if (!this.tbody) return;
    this.tbody.innerHTML = "";
    const total = this.data.length;

    if (total === 0) {
      this.tbody.innerHTML = `<tr><td colspan="100%" class="p-8 text-center text-slate-400 font-medium">No se encontraron registros</td></tr>`;
      this.updateInfo(0, 0, 0);
      this.updateButtons();
      return;
    }

    const start = (this.paginaActual - 1) * this.itemsPorPagina;
    const end = start + this.itemsPorPagina;
    const itemsPagina = this.data.slice(start, end);

    itemsPagina.forEach((item) => {
      const rowHTML = this.config.renderRow(item);
      this.tbody.insertAdjacentHTML("beforeend", rowHTML);
    });

    this.updateButtons();
    this.updateInfo(start + 1, Math.min(end, total), total);
  }

  updateButtons() {
    const totalPaginas = this.getTotalPaginas();
    if (this.btnPrev) {
      this.btnPrev.disabled = this.paginaActual === 1;
      this.btnPrev.style.opacity = this.paginaActual === 1 ? "0.5" : "1";
    }
    if (this.btnNext) {
      this.btnNext.disabled =
        this.paginaActual >= totalPaginas || totalPaginas === 0;
      this.btnNext.style.opacity =
        this.paginaActual >= totalPaginas ? "0.5" : "1";
    }
  }

  updateInfo(start, end, total) {
    if (this.infoText) {
      this.infoText.innerHTML = `Mostrando <span class="font-bold text-slate-900">${start}-${end}</span> de <span class="font-bold text-slate-900">${total}</span> registros`;
    }
  }

  next() {
    if (this.paginaActual < this.getTotalPaginas()) {
      this.paginaActual++;
      this.render();
    }
  }

  prev() {
    if (this.paginaActual > 1) {
      this.paginaActual--;
      this.render();
    }
  }
}
