/**
 * Clase Paginador Modular
 * Maneja la lógica de dividir arrays y controlar botones UI
 */
class PaginadorTabla {
    constructor(data, itemsPorPagina, callbacks) {
        this.data = data;
        this.itemsPorPagina = itemsPorPagina;
        this.paginaActual = 1;
        
        // Callbacks: Funciones que el controlador le pasa para pintar
        this.renderRow = callbacks.renderRow; // Función para pintar 1 fila
        this.updateInfo = callbacks.updateInfo; // Función para actualizar texto "1 de 50"
        
        // Elementos DOM
        this.tbody = document.getElementById(callbacks.tableBodyId);
        this.btnPrev = document.getElementById('btnPrev');
        this.btnNext = document.getElementById('btnNext');
        
        this.init();
    }

    init() {
        this.btnPrev.addEventListener('click', () => this.prev());
        this.btnNext.addEventListener('click', () => this.next());
        this.render();
    }

    setData(newData) {
        this.data = newData;
        this.paginaActual = 1; // Reset al filtrar
        this.render();
    }

    getTotalPaginas() {
        return Math.ceil(this.data.length / this.itemsPorPagina);
    }

    render() {
        this.tbody.innerHTML = '';
        const total = this.data.length;
        
        if (total === 0) {
            this.tbody.innerHTML = `<tr><td colspan="100%" class="p-8 text-center text-slate-400">Sin resultados</td></tr>`;
            this.updateInfo(0, 0, 0);
            return;
        }

        // Lógica de corte (Slice)
        const start = (this.paginaActual - 1) * this.itemsPorPagina;
        const end = start + this.itemsPorPagina;
        const itemsPagina = this.data.slice(start, end);

        // Delegar el pintado HTML al controlador específico
        itemsPagina.forEach(item => {
            const rowHTML = this.renderRow(item); // Pedimos el HTML al controlador
            this.tbody.insertAdjacentHTML('beforeend', rowHTML);
        });

        // Actualizar UI
        this.btnPrev.disabled = this.paginaActual === 1;
        this.btnNext.disabled = this.paginaActual >= this.getTotalPaginas();
        this.btnPrev.style.opacity = this.paginaActual === 1 ? '0.5' : '1';
        this.btnNext.style.opacity = this.paginaActual >= this.getTotalPaginas() ? '0.5' : '1';

        // Avisar al controlador para que actualice el texto "Mostrando X de Y"
        if(this.updateInfo) this.updateInfo(start + 1, Math.min(end, total), total);
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