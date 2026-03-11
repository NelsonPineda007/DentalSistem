// static/js/pacientesControlador.js

// --- 1. CONFIGURACIÓN Y MAPEO ---
// Diccionario para traducir nombres de DB a nombres del Formulario HTML
// Clave = Nombre en Formulario HTML, Valor = Nombre en pacientsDB
const CAMPOS_MAP = {
    'contacto_emergencia_nombre': 'contacto_nombre',
    'contacto_emergencia_telefono': 'contacto_tel',
    'enfermedades_cronicas': 'cronicas',
    'medicamentos_actuales': 'medicamentos',
    'notas_medicas': 'notas',
    'seguro_medico': 'seguro'
};

// Iconos SVG reutilizables para no ensuciar el código
const ICONS = {
    pdf: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/><path d="M8 12h8v2H8zm0 4h8v2H8z"/></svg>`,
    edit: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>`,
    trash: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>`,
    folder: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>`
};

// --- 2. BASE DE DATOS (AHORA DINÁMICA) ---
let pacientesDB = []; // Inicia vacío
let miPaginador;

// --- 3. INICIALIZACIÓN ---
document.addEventListener("DOMContentLoaded", async () => {
    if (typeof PaginadorTabla === "undefined") return console.error("Falta paginadorTabla.js");

    // 1. Inicializamos los componentes visuales
    inicializarPaginador();
    configurarBusqueda();
    configurarTabsModal();

    // 2. Traemos los datos de Laravel
    await cargarPacientesDesdeBD();

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(inicializarPaginador, 200);
    });
});

// NUEVA FUNCIÓN: Conexión al Backend
async function cargarPacientesDesdeBD() {
    try {
        // Traemos el historial completo de la base de datos
        pacientesDB = await API.get('/api/obtener-pacientes');
        
        // En lugar de meter todos los datos de golpe a la tabla, 
        // disparamos el evento del filtro para que respete lo que dice el <select>
        const filterEstado = document.getElementById("filterEstado");
        if (filterEstado) {
            filterEstado.dispatchEvent(new Event('change'));
        } else {
            miPaginador.setData(pacientesDB);
        }
        
    } catch (error) {
        console.error("Error cargando pacientes:", error);
    }
}

function calcularItemsPorPagina() {
    const container = document.getElementById("tableContainer");
    if (!container) return 5;
    const items = Math.floor((container.clientHeight - 140) / 76); // 140 = header+footer aprox
    return items > 3 ? items : 3;
}

function inicializarPaginador() {
    miPaginador = new PaginadorTabla(pacientesDB, calcularItemsPorPagina(), {
        tableBodyId: "patientsTableBody",
        containerId: "tableContainer",
        renderRow: (p) => {
            const edad = p.fecha_nacimiento ? new Date().getFullYear() - new Date(p.fecha_nacimiento).getFullYear() : "-";
            
            // Adaptación para MySQL: Evaluamos el texto del estado
            const esActivo = p.estado === 'Activo';
            const estadoClass = esActivo ? "bg-emerald-100 text-emerald-700 border-emerald-200" : "bg-slate-100 text-slate-500 border-slate-200";

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-blue-800 text-sm">${p.numero_expediente}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${p.nombre} ${p.apellido}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">${p.genero || ""}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">${p.telefono || "-"}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">${edad} años</td>
                    <td class="px-6 py-4"><span class="${estadoClass} text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">${p.estado}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="expediente?id=${p.id}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Ver Expediente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            </a>
                            <button onclick="window.imprimirExpediente(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100" title="PDF">${ICONS.pdf}</button>
                            <button onclick="window.abrirModalEdicion(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm border border-emerald-100" title="Editar">${ICONS.edit}</button>
                            <button onclick="window.eliminarPaciente(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200" title="Eliminar">${ICONS.trash}</button>
                        </div>
                    </td>
                </tr>`;
        },
        updateInfo: (start, end, total) => {
            const info = document.getElementById("paginationInfo");
            if (info) info.innerHTML = `Mostrando <span class="font-bold text-slate-900">${start}-${end}</span> de <span class="font-bold text-slate-900">${total}</span> pacientes`;
        }
    });
}

function configurarBusqueda() {
    const searchInput = document.getElementById("searchInput");
    const filterEstado = document.getElementById("filterEstado");

    function aplicarFiltros() {
        const term = searchInput ? searchInput.value.toLowerCase() : '';
        const estadoSelect = filterEstado ? filterEstado.value : ''; // "1", "0", o ""

        const filtrados = pacientesDB.filter(p => {
            // 1. Búsqueda por texto (nombre, apellido, numero_expediente)
            const coincideTexto = (p.nombre + " " + p.apellido).toLowerCase().includes(term) || 
                                  p.numero_expediente.toLowerCase().includes(term);
            
            // 2. Filtro de estado
            let coincideEstado = true;
            if (estadoSelect !== "") {
                // Convertimos el "Activo" de MySQL a "1" o "0" para compararlo con el Select
                const estadoReal = p.estado === 'Activo' ? "1" : "0";
                coincideEstado = estadoReal === estadoSelect;
            }

            return coincideTexto && coincideEstado;
        });

        // Tu paginador universal hace el resto de la magia
        miPaginador.setData(filtrados);
    }

    if (searchInput) searchInput.addEventListener("input", aplicarFiltros);
    if (filterEstado) filterEstado.addEventListener("change", aplicarFiltros);
}


function configurarTabsModal() {
    document.querySelectorAll(".tab-btn").forEach((tab) => {
        tab.addEventListener("click", () => {
            document.querySelectorAll(".tab-btn").forEach((t) => {
                t.classList.remove("border-blue-800", "text-blue-800");
                t.classList.add("border-transparent", "text-slate-500");
            });
            tab.classList.remove("border-transparent", "text-slate-500");
            tab.classList.add("border-blue-800", "text-blue-800");
            document.querySelectorAll(".tab-content").forEach((c) => c.classList.add("hidden"));
            document.getElementById(tab.dataset.target).classList.remove("hidden");
        });
    });
}



// --- FUNCIONES GLOBALES OPTIMIZADAS ---

window.openModal = function (modalID, mode = "add") {
    const modal = document.getElementById(modalID);
    const form = document.getElementById("formPaciente");
    
    // Nos aseguramos de que siempre se abra en la primera pestaña
    const tabPersonal = document.querySelector('.tab-btn[data-target="tab-personal"]');
    if (tabPersonal) tabPersonal.click();

    if (mode === "add") {
        document.getElementById("modalTitle").innerText = "Nuevo Paciente";
        form.reset();
        form.id.value = "";
        
        // --- AQUÍ ESTÁ LA MAGIA DEL NUEVO FORMATO ---
        // 1. Buscamos el expediente con el número más alto para evitar duplicados al eliminar
        let maxNum = 0;
        pacientesDB.forEach(p => {
            if (p.numero_expediente && p.numero_expediente.startsWith('EXP-')) {
                const num = parseInt(p.numero_expediente.replace('EXP-', ''));
                if (num > maxNum) maxNum = num;
            }
        });
        
        // 2. Generamos el nuevo código con el formato EXP-XXX
        form.expediente.value = `EXP-${String(maxNum + 1).padStart(3, "0")}`;
        // ---------------------------------------------
        
        form.ciudad.value = "San Salvador";
    } else {
        document.getElementById("modalTitle").innerText = "Editar Paciente";
    }
    
    modal.classList.remove("hidden");
    setTimeout(() => {
        const backdrop = modal.querySelector(".modal-backdrop");
        if (backdrop) backdrop.classList.remove("opacity-0");
        const panel = modal.querySelector(".modal-panel");
        if (panel) {
            panel.classList.remove("opacity-0", "translate-y-4", "sm:scale-95");
            panel.classList.add("opacity-100", "translate-y-0", "sm:scale-100");
        }
    }, 10);
};

window.closeModal = function (modalID) {
    const modal = document.getElementById(modalID);
    modal.querySelector(".modal-backdrop").classList.add("opacity-0");
    const panel = modal.querySelector(".modal-panel");
    panel.classList.add("opacity-0", "translate-y-4", "sm:scale-95");
    panel.classList.remove("opacity-100", "translate-y-0", "sm:scale-100");
    setTimeout(() => modal.classList.add("hidden"), 300);
};

// OPTIMIZADA: Rellena el formulario automáticamente usando el mapa
window.abrirModalEdicion = function (id) {
    const p = pacientesDB.find((x) => x.id === id);
    if (!p) return;
    
    const form = document.getElementById("formPaciente");
    
    // Recorremos todos los inputs del formulario
    Array.from(form.elements).forEach(input => {
        if(!input.name) return;
        
        // Buscamos el nombre equivalente en la DB
        const dbKey = CAMPOS_MAP[input.name] || input.name;
        
        // Si el campo existe en el paciente, lo asignamos
        if(p[dbKey] !== undefined && p[dbKey] !== null) {
            input.value = p[dbKey];
        }
    });

    // --- CASOS ESPECIALES MANUALES ---
    form.id.value = p.id; // Aseguramos el ID oculto
    form.expediente.value = p.numero_expediente; // ¡Esto arregla tu error 422!
    form.activo.value = p.estado === 'Activo' ? "1" : "0"; // Muestra el estado correcto
    
    if(p.fecha_nacimiento) {
        // En Laravel las fechas a veces vienen con horas (ej. 2024-05-10T00:00:00.000000Z)
        // Esto la recorta para que el input type="date" la acepte sin errores
        form.fecha_nacimiento.value = p.fecha_nacimiento.split('T')[0]; 
    }

    window.openModal("modalPacientes", "edit");
};

window.eliminarPaciente = async function (id) {
    // 1. Un mensaje claro de lo que realmente va a pasar
    if (confirm("¿Estás seguro de archivar este expediente?\n\nEl paciente pasará a estado 'Inactivo' y se ocultará de la lista principal, pero su historial médico y pagos se conservarán intactos.")) {
        try {
            // 2. Usamos el verbo DELETE, que es el estándar RESTful
            await API.delete(`/api/pacientes/${id}`);
            
            // 3. Avisamos y recargamos la tabla
            alert("Expediente archivado (Inactivo) correctamente.");
            await cargarPacientesDesdeBD();
            
        } catch (error) {
            console.error("Error al archivar:", error);
            alert("No se pudo archivar el expediente.");
        }
    }
};

window.imprimirExpediente = function (id) {
    const p = pacientesDB.find((x) => x.id === id);
    if (!p || typeof ReportePDF === "undefined") return;
    
    const edad = p.fecha_nacimiento ? new Date().getFullYear() - new Date(p.fecha_nacimiento).getFullYear() : "N/A";

    ReportePDF.generar({
        folio: p.expediente,
        nombreArchivo: `Expediente_${p.expediente}`,
        data: {
            nombre: `${p.nombre} ${p.apellido}`,
            nacimiento: p.fecha_nacimiento,
            edad: `${edad} años`,
            genero: p.genero,
            telefono: p.telefono,
            email: p.email,
            direccion: `${p.direccion}, ${p.ciudad}`,
            cp: p.codigo_postal,
            emergencia_nombre: p.contacto_nombre,
            emergencia_tel: p.contacto_tel,
            seguro: p.seguro,
            alergias: p.alergias,
            cronicas: p.cronicas,
            medicamentos: p.medicamentos,
            notas: p.notas,
        },
        citas: p.citas || [],
        tratamientos: p.tratamientos || [],
    });
};

// OPTIMIZADA: Guarda datos dinámicamente en MySQL
// OPTIMIZADA: Guarda datos dinámicamente usando la API global
window.guardarDatos = async function () {
    const form = document.getElementById("formPaciente");
    const formData = new FormData(form);
    const id = formData.get("id");
    
    // Convertir FormData a Objeto simple
    let datosForm = Object.fromEntries(formData.entries());

    // Validar requeridos
    if (!datosForm.nombre || !datosForm.apellido || !datosForm.telefono) {
        return alert("Por favor complete los campos obligatorios (*)");
    }

    // Cambiar la llave del expediente al nombre real de la base de datos
    datosForm.numero_expediente = datosForm.expediente;
    delete datosForm.expediente;

    // Traducir campos del Formulario -> DB usando el mapa
    Object.keys(CAMPOS_MAP).forEach(formKey => {
        if(datosForm[formKey] !== undefined) {
            datosForm[CAMPOS_MAP[formKey]] = datosForm[formKey];
            delete datosForm[formKey];
        }
    });

    

    // Asegurar que el estado y fechas vayan limpios
    datosForm.estado = parseInt(datosForm.activo) === 1 ? 'Activo' : 'Inactivo';
    if (!datosForm.fecha_nacimiento) delete datosForm.fecha_nacimiento;

    try {
        const btnGuardar = document.getElementById("btnGuardar");
        btnGuardar.innerText = "Guardando...";
        btnGuardar.disabled = true;

        if (id) {
            // LÓGICA DE ACTUALIZAR (PUT)
            await API.put(`/api/pacientes/${id}`, datosForm);
            alert("Paciente actualizado correctamente");
        } else {
            // LÓGICA DE CREAR NUEVO (POST)
            await API.post('/api/guardar-paciente', datosForm);
            alert("Paciente guardado correctamente");
        }

        window.closeModal("modalPacientes");
        form.reset();
        
        // Refrescar tabla
        await cargarPacientesDesdeBD();

    } catch (error) {
        console.error("Error al guardar:", error);
        alert("Error al guardar. Verifica la consola.");
    } finally {
        const btnGuardar = document.getElementById("btnGuardar");
        btnGuardar.innerText = "Guardar";
        btnGuardar.disabled = false;
    }

    
};
