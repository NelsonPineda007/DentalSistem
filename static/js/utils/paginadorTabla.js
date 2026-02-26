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
    const availableHeight = scrollContainer.clientHeight;
    const thead = scrollContainer.querySelector('thead');
    const theadHeight = thead ? thead.offsetHeight : 55;
    const rowHeight = 75;
    const spaceForRows = availableHeight - theadHeight;
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

    // Dibuja solo las filas reales (SE ELIMINARON LAS FILAS FANTASMAS)
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