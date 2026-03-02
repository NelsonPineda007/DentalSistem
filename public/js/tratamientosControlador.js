// static/js/tratamientosControlador.js

const ICONS = {
    edit: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>`,
    trash: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>`
};

// --- 1. BASE DE DATOS SIMULADA ---
let tratamientosDB = [
    { id: 1, codigo: 'LMP-001', nombre: 'Limpieza Dental', descripcion: 'Limpieza profunda y profilaxis dental', categoria: 'Preventivo', duracion_estimada: 30, costo_base: 35.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() },
    { id: 2, codigo: 'RST-002', nombre: 'Resina Dental', descripcion: 'Restauración con resina compuesta', categoria: 'Restaurador', duracion_estimada: 45, costo_base: 50.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() },
    { id: 3, codigo: 'END-003', nombre: 'Endodoncia', descripcion: 'Tratamiento de conducto radicular', categoria: 'Endodoncia', duracion_estimada: 90, costo_base: 150.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() },
    { id: 4, codigo: 'ORT-001', nombre: 'Ajuste Brackets', descripcion: 'Control mensual de ortodoncia', categoria: 'Ortodoncia', duracion_estimada: 20, costo_base: 40.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() },
    { id: 5, codigo: 'EXT-001', nombre: 'Extracción Simple', descripcion: 'Extracción de pieza dental sin cirugía', categoria: 'Cirugía', duracion_estimada: 40, costo_base: 45.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() },
    { id: 6, codigo: 'EXT-002', nombre: 'Extracción Cordal', descripcion: 'Extracción de muela del juicio', categoria: 'Cirugía', duracion_estimada: 60, costo_base: 120.00, requiere_cita: true, activo: false, creado_en: new Date().toISOString() },
    { id: 7, codigo: 'BLQ-001', nombre: 'Blanqueamiento', descripcion: 'Blanqueamiento dental láser', categoria: 'Estética', duracion_estimada: 60, costo_base: 200.00, requiere_cita: true, activo: true, creado_en: new Date().toISOString() }
];

let miPaginador;

// --- 2. INICIALIZACIÓN ---
document.addEventListener("DOMContentLoaded", () => {
    if (typeof PaginadorTabla === "undefined") return console.error("Falta paginadorTabla.js");

    inicializarPaginador();
    configurarFiltros();
    actualizarEstadisticas(tratamientosDB); // Pintar stats iniciales

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(inicializarPaginador, 200);
    });
});

// --- LÓGICA DE ESTADÍSTICAS Y GRÁFICAS (RESTAURADO A TU DISEÑO ORIGINAL) ---
function actualizarEstadisticas(datos) {
    if(!datos) return;

    const total = datos.length;
    const activos = datos.filter(t => t.activo).length;
    const categorias = new Set(datos.map(t => t.categoria)).size;
    const costoPromedio = total > 0 ? datos.reduce((sum, t) => sum + parseFloat(t.costo_base), 0) / total : 0;

    // Actualizar Textos
    if(document.getElementById('statTotal')) document.getElementById('statTotal').textContent = total;
    if(document.getElementById('statActivos')) document.getElementById('statActivos').textContent = activos;
    if(document.getElementById('statCategorias')) document.getElementById('statCategorias').textContent = categorias;
    if(document.getElementById('statCosto')) document.getElementById('statCosto').textContent = '$' + costoPromedio.toFixed(2);

    // Actualizar Sparklines (Llamando a la función global en charts.js)
    // Generamos datos falsos dinámicos basados en los reales para que las gráficas se muevan
    if(typeof window.drawSparkline === 'function') {
        const d1 = [total-2, total-1, total+1, total-1, total+2, total];
        window.drawSparkline('sparkTotal', d1, '#2563eb', 'rgba(37, 99, 235, 0.2)'); // Azul
        
        const d2 = [activos-1, activos+1, activos-2, activos, activos+1, activos];
        window.drawSparkline('sparkActivos', d2, '#059669', 'rgba(5, 150, 105, 0.2)'); // Verde
        
        const d3 = [categorias-1, categorias, categorias+1, categorias-1, categorias, categorias];
        window.drawSparkline('sparkCategorias', d3, '#7c3aed', 'rgba(124, 58, 237, 0.2)'); // Morado
        
        const d4 = [costoPromedio-10, costoPromedio+5, costoPromedio-5, costoPromedio+15, costoPromedio-2, costoPromedio];
        window.drawSparkline('sparkCosto', d4, '#ca8a04', 'rgba(202, 138, 4, 0.2)'); // Amarillo
    }
}

// --- RESTO DEL CÓDIGO (Tabla y Modal - Igual al paso anterior) ---

function calcularItemsPorPagina() {
    const container = document.getElementById("tableContainer");
    if (!container) return 5;
    const items = Math.floor((container.clientHeight - 140) / 76);
    return items > 3 ? items : 3;
}

function inicializarPaginador() {
    miPaginador = new PaginadorTabla(tratamientosDB, calcularItemsPorPagina(), {
        tableBodyId: "tratamientosTableBody",
        containerId: "tableContainer",
        renderRow: (t) => {
            const estadoClass = t.activo ? "bg-emerald-100 text-emerald-700 border-emerald-200" : "bg-slate-100 text-slate-500 border-slate-200";
            const estadoTexto = t.activo ? "Activo" : "Inactivo";
            const costo = parseFloat(t.costo_base).toFixed(2);

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-blue-800 text-sm">${t.codigo}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${t.nombre}</span>
                            <span class="text-[10px] text-slate-400 truncate max-w-[200px]">${t.descripcion || 'Sin descripción'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600">${t.categoria}</td>
                    <td class="px-6 py-4 text-sm font-bold text-emerald-600">$${costo}</td>
                    <td class="px-6 py-4"><span class="${estadoClass} text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">${estadoTexto}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="window.abrirModalEdicion(${t.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm border border-emerald-100">${ICONS.edit}</button>
                            <button onclick="window.eliminarTratamiento(${t.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200">${ICONS.trash}</button>
                        </div>
                    </td>
                </tr>`;
        },
        updateInfo: (start, end, total) => {
            const info = document.getElementById("paginationInfo");
            if (info) info.innerHTML = `Mostrando <span class="font-bold text-slate-900">${start}-${end}</span> de <span class="font-bold text-slate-900">${total}</span> tratamientos`;
        }
    });
}

function configurarFiltros() {
    const searchInput = document.getElementById("searchInput");
    const filterCategoria = document.getElementById("filterCategoria");

    function aplicarFiltros() {
        const term = searchInput ? searchInput.value.toLowerCase() : '';
        const categoria = filterCategoria ? filterCategoria.value : '';

        const filtrados = tratamientosDB.filter(t => {
            const coincideTexto = t.nombre.toLowerCase().includes(term) || t.codigo.toLowerCase().includes(term);
            const coincideCategoria = categoria === "" || t.categoria === categoria;
            return coincideTexto && coincideCategoria;
        });

        miPaginador.setData(filtrados);
        actualizarEstadisticas(filtrados); // Actualiza stats al filtrar
    }

    if (searchInput) searchInput.addEventListener("input", aplicarFiltros);
    if (filterCategoria) filterCategoria.addEventListener("change", aplicarFiltros);
}

window.openModal = function (modalID, mode = "add") {
    const modal = document.getElementById(modalID);
    const form = document.getElementById("formTratamiento");

    if (mode === "add") {
        document.getElementById("modalTitle").innerText = "Nuevo Tratamiento";
        form.reset();
        form.id.value = "";
        form.activo.checked = true; 
    } else {
        document.getElementById("modalTitle").innerText = "Editar Tratamiento";
    }
    
    modal.classList.remove("hidden");
    setTimeout(() => {
        modal.querySelector(".modal-backdrop").classList.remove("opacity-0");
        modal.querySelector(".modal-panel").classList.remove("opacity-0", "translate-y-4", "sm:scale-95");
        modal.querySelector(".modal-panel").classList.add("opacity-100", "translate-y-0", "sm:scale-100");
    }, 10);
};

window.closeModal = function (modalID) {
    const modal = document.getElementById(modalID);
    modal.querySelector(".modal-backdrop").classList.add("opacity-0");
    modal.querySelector(".modal-panel").classList.add("opacity-0", "translate-y-4", "sm:scale-95");
    modal.querySelector(".modal-panel").classList.remove("opacity-100", "translate-y-0", "sm:scale-100");
    setTimeout(() => modal.classList.add("hidden"), 300);
};

window.abrirModalEdicion = function (id) {
    const t = tratamientosDB.find((x) => x.id === id);
    if (!t) return;
    
    const form = document.getElementById("formTratamiento");
    
    Array.from(form.elements).forEach(input => {
        if(!input.name) return;
        
        if(input.type === 'checkbox') {
            input.checked = t[input.name] === true;
        } else if (input.name === 'requiere_cita') {
            input.value = t.requiere_cita ? "true" : "false";
        } else if (t[input.name] !== undefined) {
            input.value = t[input.name];
        }
    });

    window.openModal("modalTratamientos", "edit");
};

window.eliminarTratamiento = function (id) {
    if (confirm("¿Eliminar este tratamiento permanentemente?")) {
        tratamientosDB = tratamientosDB.filter((t) => t.id !== id);
        document.getElementById("searchInput").dispatchEvent(new Event('input')); 
    }
};

window.guardarDatos = function () {
    const form = document.getElementById("formTratamiento");
    const formData = new FormData(form);
    const id = formData.get("id");
    
    let datosForm = Object.fromEntries(formData.entries());

    if (!datosForm.codigo || !datosForm.nombre || !datosForm.costo_base) {
        return alert("Por favor complete los campos obligatorios (*)");
    }

    datosForm.activo = form.activo.checked; 
    datosForm.requiere_cita = datosForm.requiere_cita === 'true';
    datosForm.costo_base = parseFloat(datosForm.costo_base);
    datosForm.duracion_estimada = datosForm.duracion_estimada ? parseInt(datosForm.duracion_estimada) : 0;

    if (id) {
        const index = tratamientosDB.findIndex((t) => t.id == id);
        if (index !== -1) {
            datosForm.creado_en = tratamientosDB[index].creado_en; 
            tratamientosDB[index] = { ...tratamientosDB[index], ...datosForm, id: parseInt(id) };
        }
    } else {
        datosForm.creado_en = new Date().toISOString(); 
        tratamientosDB.push({ ...datosForm, id: Date.now() });
    }

    alert("Tratamiento guardado correctamente");
    window.closeModal("modalTratamientos");
    document.getElementById("searchInput").dispatchEvent(new Event('input')); 
};