const ICONS = {
    edit: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>`,
    trash: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>`
};

let tratamientosDB = [];
let miPaginador;

document.addEventListener("DOMContentLoaded", async () => {
    if (typeof PaginadorTabla === "undefined") return console.error("Falta paginadorTabla.js");

    await cargarCategorias(); 
    configurarFiltros();
    inicializarPaginador();
    await cargarTratamientosDesdeBD();

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(inicializarPaginador, 200);
    });
});

async function cargarCategorias() {
    try {
        const categorias = await API.get('/api/categorias-tratamientos');
        console.log("✅ Categorías cargadas desde MySQL:", categorias);
        
        let opcionesFiltro = '<option value="">Todas las categorías</option>';
        let opcionesForm = '<option value="">Seleccione una categoría</option>';

        categorias.forEach(c => {
            opcionesFiltro += `<option value="${c.id}">${c.nombre}</option>`;
            opcionesForm += `<option value="${c.id}">${c.nombre}</option>`;
        });

        const selectFiltro = document.getElementById("filterCategoria");
        const selectForm = document.querySelector('select[name="categoria_id"]');

        if (selectFiltro) selectFiltro.innerHTML = opcionesFiltro;
        if (selectForm) selectForm.innerHTML = opcionesForm;

    } catch (error) {
        console.error("❌ Error cargando categorías:", error);
    }
}

async function cargarTratamientosDesdeBD() {
    try {
        tratamientosDB = await API.get('/api/obtener-tratamientos');
        const filterCategoria = document.getElementById("filterCategoria");
        if (filterCategoria) {
            filterCategoria.dispatchEvent(new Event('change'));
        } else {
            miPaginador.setData(tratamientosDB);
            actualizarEstadisticas(tratamientosDB);
        }
    } catch (error) {
        console.error("Error cargando tratamientos:", error);
    }
}

function configurarFiltros() {
    const searchInput = document.getElementById("searchInput");
    const filterCategoria = document.getElementById("filterCategoria");

    function aplicarFiltros() {
        const term = searchInput ? searchInput.value.toLowerCase() : '';
        const categoria = filterCategoria ? filterCategoria.value : '';

        const filtrados = tratamientosDB.filter(t => {
            const coincideTexto = t.nombre.toLowerCase().includes(term) || t.codigo.toLowerCase().includes(term);
            const coincideCategoria = categoria === "" || String(t.categoria_id) === String(categoria);
            return coincideTexto && coincideCategoria;
        });

        miPaginador.setData(filtrados);
        actualizarEstadisticas(filtrados);
    }

    if (searchInput) searchInput.addEventListener("input", aplicarFiltros);
    if (filterCategoria) filterCategoria.addEventListener("change", aplicarFiltros);
}

function actualizarEstadisticas(datos) {
    if(!datos) return;

    const total = datos.length;
    const activos = datos.filter(t => t.estado === 'Activo').length;
    const categorias = new Set(datos.filter(t => t.categoria_id).map(t => t.categoria_id)).size;
    const costoPromedio = total > 0 ? datos.reduce((sum, t) => sum + parseFloat(t.costo_base), 0) / total : 0;

    if(document.getElementById('statTotal')) document.getElementById('statTotal').textContent = total;
    if(document.getElementById('statActivos')) document.getElementById('statActivos').textContent = activos;
    if(document.getElementById('statCategorias')) document.getElementById('statCategorias').textContent = categorias;
    if(document.getElementById('statCosto')) document.getElementById('statCosto').textContent = '$' + costoPromedio.toFixed(2);

    if(typeof window.drawSparkline === 'function') {
        window.drawSparkline('sparkTotal', [total-2, total-1, total+1, total-1, total+2, total], '#2563eb', 'rgba(37, 99, 235, 0.2)'); 
        window.drawSparkline('sparkActivos', [activos-1, activos+1, activos-2, activos, activos+1, activos], '#059669', 'rgba(5, 150, 105, 0.2)'); 
        window.drawSparkline('sparkCategorias', [categorias-1, categorias, categorias+1, categorias-1, categorias, categorias], '#7c3aed', 'rgba(124, 58, 237, 0.2)'); 
        window.drawSparkline('sparkCosto', [costoPromedio-10, costoPromedio+5, costoPromedio-5, costoPromedio+15, costoPromedio-2, costoPromedio], '#ca8a04', 'rgba(202, 138, 4, 0.2)'); 
    }
}

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
            const esActivo = t.estado === 'Activo';
            const estadoClass = esActivo ? "bg-emerald-100 text-emerald-700 border-emerald-200" : "bg-slate-100 text-slate-500 border-slate-200";
            const estadoTexto = esActivo ? "Activo" : "Inactivo";
            const costoFormateado = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(t.costo_base || 0);
            const nombreCategoria = t.categoria ? t.categoria.nombre : '<span class="text-rose-400 italic font-medium">Sin Categoría</span>';

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-blue-800 text-sm">${t.codigo}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${t.nombre}</span>
                            <span class="text-[10px] text-slate-400 truncate max-w-[200px]">${t.descripcion || 'Sin descripción'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600">${nombreCategoria}</td>
                    <td class="px-6 py-4 font-bold text-slate-700 text-sm">${costoFormateado}</td>
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
        if (t[input.name] !== undefined && t[input.name] !== null) {
            input.value = t[input.name];
        }
    });

    form.id.value = t.id;
    form.activo.checked = t.estado === 'Activo'; 
    form.requiere_cita.value = t.requiere_cita ? "true" : "false";

    window.openModal("modalTratamientos", "edit");
};

window.eliminarTratamiento = async function (id) {
    if (confirm("¿Estás seguro de archivar este tratamiento?\n\nPasará a estado Inactivo y no se mostrará por defecto.")) {
        try {
            await API.delete(`/api/tratamientos/${id}`);
            alert("Tratamiento archivado correctamente.");
            await cargarTratamientosDesdeBD(); 
        } catch (error) {
            console.error("Error al archivar:", error);
            alert("Hubo un error al intentar archivar el tratamiento.");
        }
    }
};

window.guardarDatos = async function () {
    const form = document.getElementById("formTratamiento");
    const formData = new FormData(form);
    const id = formData.get("id");
    
    let datosForm = Object.fromEntries(formData.entries());

    if (!datosForm.codigo || !datosForm.nombre || !datosForm.costo_base) {
        return alert("Por favor complete los campos obligatorios (*)");
    }

    datosForm.estado = form.activo.checked ? 'Activo' : 'Inactivo';
    datosForm.requiere_cita = datosForm.requiere_cita === 'true' ? 1 : 0;
    datosForm.costo_base = parseFloat(datosForm.costo_base);
    datosForm.duracion_estimada = datosForm.duracion_estimada ? parseInt(datosForm.duracion_estimada) : null;

    try {
        if (id) {
            await API.put(`/api/tratamientos/${id}`, datosForm);
            alert("Tratamiento actualizado en la base de datos");
        } else {
            await API.post('/api/guardar-tratamiento', datosForm);
            alert("Tratamiento guardado en la base de datos");
        }

        window.closeModal("modalTratamientos");
        await cargarTratamientosDesdeBD(); 
        
    } catch (error) {
        console.error("Error al guardar:", error);
        alert("Ocurrió un error al guardar. Revisa la consola.");
    }
};