/**
 * Clase Paginador Universal Inteligente
 */
class PaginadorTabla {
  constructor(data, itemsPorPagina, config) {
    this.data = data;
    this.paginaActual = 1;
    this.config = config;
    this.containerId = config.containerId || "tableContainer";

    this.tbody = document.getElementById(config.tableBodyId);
    const container = document.getElementById(this.containerId);

    this.btnPrev = container ? container.querySelector(".btn-prev") : document.getElementById("btnPrev");
    this.btnNext = container ? container.querySelector(".btn-next") : document.getElementById("btnNext");
    this.infoText = container ? container.querySelector(".pagination-info") : document.getElementById("paginationInfo");

    this.isAuto = itemsPorPagina === "auto";
    this.manualItems = typeof itemsPorPagina === "number" ? itemsPorPagina : 5;
    this.itemsPorPagina = this.isAuto ? 3 : this.manualItems;

    this.init();
  }

  init() {
    if (this.btnPrev) this.btnPrev.addEventListener("click", () => this.prev());
    if (this.btnNext) this.btnNext.addEventListener("click", () => this.next());

    if (this.isAuto) {
      // Usamos el contenedor padre del tbody (el div con overflow) para medir el espacio real
      const scrollContainer = this.tbody ? this.tbody.parentElement.parentElement : null;
      
      if (scrollContainer) {
        const observer = new ResizeObserver(() => {
          window.requestAnimationFrame(() => {
            this.recalcularYRenderizar(scrollContainer);
          });
        });
        observer.observe(scrollContainer);
      }
    }

    this.render();
  }

  calcularItemsAuto(scrollContainer) {
    if (!scrollContainer) return 3;

    // 1. Altura total disponible en el div scrolleable
    const availableHeight = scrollContainer.clientHeight;
    
    // 2. Altura del thead
    const thead = scrollContainer.querySelector('thead');
    const theadHeight = thead ? thead.offsetHeight : 55;

    // 3. Altura de cada fila (fija)
    const rowHeight = 75;

    // 4. Espacio para las filas = Alto total - Alto del Thead
    const spaceForRows = availableHeight - theadHeight;
    
    // 5. ¿Cuántas caben?
    const items = Math.floor(spaceForRows / rowHeight);

    return items > 3 ? items : 3;
  }

  recalcularYRenderizar(scrollContainer) {
    if (!this.isAuto) return;
    
    const nuevosItems = this.calcularItemsAuto(scrollContainer);

    if (nuevosItems !== this.itemsPorPagina) {
      this.itemsPorPagina = nuevosItems;
      const totalPaginas = this.getTotalPaginas();
      if (this.paginaActual > totalPaginas) this.paginaActual = totalPaginas || 1;
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

    // 1. Datos reales
    itemsPagina.forEach((item) => {
      const rowHTML = this.config.renderRow(item);
      this.tbody.insertAdjacentHTML("beforeend", rowHTML);
    });

    // 2. Filas fantasma
    const emptyRowsCount = this.itemsPorPagina - itemsPagina.length;
    for(let i = 0; i < emptyRowsCount; i++) {
        // La clase h-[75px] es OBLIGATORIA aquí para que el cálculo coincida con la realidad
        this.tbody.insertAdjacentHTML("beforeend", `<tr class="h-[75px] pointer-events-none border-b border-transparent"><td colspan="100%"></td></tr>`);
    }

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
      this.btnNext.disabled = this.paginaActual >= totalPaginas || totalPaginas === 0;
      this.btnNext.style.opacity = this.paginaActual >= totalPaginas ? "0.5" : "1";
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