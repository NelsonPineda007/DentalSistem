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
    trash: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>`
};

// --- 2. BASE DE DATOS SIMULADA ---
let pacientesDB = [
    { id: 1, expediente: "2024-001", nombre: "María", apellido: "González", telefono: "7777-8888", email: "maria.g@email.com", fecha_nacimiento: "1990-05-15", genero: "Femenino", direccion: "Col. San Benito", ciudad: "San Salvador", codigo_postal: "01101", contacto_nombre: "Pedro González", contacto_tel: "6666-5555", seguro: "Seguros del País", alergias: "Ninguna", cronicas: "Ninguna", medicamentos: "Ninguno", notas: "", activo: 1, citas: [{ fecha: "15/01/2024", motivo: "Revisión General", estado: "Completada" }, { fecha: "20/03/2024", motivo: "Limpieza Dental", estado: "Completada" }], tratamientos: [{ nombre: "Limpieza Dental", fecha: "15/01/2024", costo: "$35.00" }, { nombre: "Consulta General", fecha: "20/03/2024", costo: "$25.00" }] },
    { id: 2, expediente: "2024-002", nombre: "Carlos", apellido: "Martínez", telefono: "6666-7777", email: "carlos.m@email.com", fecha_nacimiento: "1985-08-22", genero: "Masculino", direccion: "Col. Escalón", ciudad: "San Salvador", codigo_postal: "01102", contacto_nombre: "Ana Martínez", contacto_tel: "7777-9999", seguro: "Aseguradora Suiza", alergias: "Penicilina", cronicas: "Hipertensión", medicamentos: "Losartán 50mg", notas: "Paciente nervioso", activo: 0, citas: [{ fecha: "10/02/2024", motivo: "Dolor de muela", estado: "Completada" }], tratamientos: [{ nombre: "Extracción Dental", fecha: "10/02/2024", costo: "$75.00" }] },
    { id: 3, expediente: "2024-003", nombre: "Ana", apellido: "Rodríguez", telefono: "7888-9999", email: "ana.r@email.com", fecha_nacimiento: "1995-03-10", genero: "Femenino", direccion: "Col. Miramonte", ciudad: "San Salvador", codigo_postal: "01103", contacto_nombre: "Luis Rodríguez", contacto_tel: "7888-0000", seguro: "Plan Básico", alergias: "Látex", cronicas: "Asma", medicamentos: "Salbutamol", notas: "Usar guantes de nitrilo", activo: 1, citas: [], tratamientos: [] },
    { id: 4, expediente: "2024-004", nombre: "Jose", apellido: "Hernández", telefono: "7555-4444", fecha_nacimiento: "1988-11-30", genero: "Masculino", activo: 1, citas: [], tratamientos: [] },
    { id: 5, expediente: "2024-005", nombre: "Sofía", apellido: "López", telefono: "6777-8888", fecha_nacimiento: "1992-07-18", genero: "Femenino", activo: 1, citas: [], tratamientos: [] },
    { id: 6, expediente: "2024-006", nombre: "Pedro", apellido: "Ramírez", telefono: "7111-2222", fecha_nacimiento: "1990-01-01", genero: "Masculino", activo: 0, citas: [], tratamientos: [] },
    { id: 7, expediente: "2024-007", nombre: "Lucía", apellido: "Méndez", telefono: "7000-1111", fecha_nacimiento: "1998-05-20", genero: "Femenino", activo: 1, citas: [], tratamientos: [] },
    { id: 8, expediente: "2024-008", nombre: "Jorge", apellido: "Campos", telefono: "7222-9999", fecha_nacimiento: "1982-11-11", genero: "Masculino", activo: 1, citas: [], tratamientos: [] },
    { id: 9, expediente: "2024-009", nombre: "Elena", apellido: "Vargas", telefono: "7888-2222", fecha_nacimiento: "1991-03-30", genero: "Femenino", activo: 1, citas: [], tratamientos: [] },
    { id: 10, expediente: "2024-010", nombre: "Roberto", apellido: "Díaz", telefono: "7555-8888", fecha_nacimiento: "1975-09-15", genero: "Masculino", activo: 0, citas: [], tratamientos: [] },
    { id: 11, expediente: "2024-011", nombre: "Patricia", apellido: "Rivas", telefono: "7111-7777", fecha_nacimiento: "2000-01-25", genero: "Femenino", activo: 1, citas: [], tratamientos: [] },
    { id: 12, expediente: "2024-012", nombre: "Mario", apellido: "Castillo", telefono: "7333-4444", fecha_nacimiento: "1989-07-07", genero: "Masculino", activo: 1, citas: [], tratamientos: [] }
];

let miPaginador;

// --- 3. INICIALIZACIÓN ---
document.addEventListener("DOMContentLoaded", () => {
    if (typeof PaginadorTabla === "undefined") return console.error("Falta paginadorTabla.js");

    inicializarPaginador();
    configurarBusqueda();
    configurarTabsModal();

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(inicializarPaginador, 200);
    });
});

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
            const estadoClass = p.activo ? "bg-emerald-100 text-emerald-700 border-emerald-200" : "bg-slate-100 text-slate-500 border-slate-200";
            const estadoTexto = p.activo ? "Activo" : "Inactivo";

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-blue-800 text-sm">${p.expediente}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${p.nombre} ${p.apellido}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">${p.genero || ""}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">${p.telefono || "-"}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">${edad} años</td>
                    <td class="px-6 py-4"><span class="${estadoClass} text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">${estadoTexto}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
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
    const input = document.getElementById("searchInput");
    if (!input) return;
    input.addEventListener("input", (e) => {
        const term = e.target.value.toLowerCase();
        miPaginador.setData(pacientesDB.filter(p => (p.nombre + " " + p.apellido).toLowerCase().includes(term) || p.expediente.toLowerCase().includes(term)));
    });
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
    document.querySelector('.tab-btn[data-target="tab-personal"]').click();

    if (mode === "add") {
        document.getElementById("modalTitle").innerText = "Nuevo Paciente";
        form.reset();
        form.id.value = "";
        form.expediente.value = `2024-${String(pacientesDB.length + 1).padStart(3, "0")}`;
        form.ciudad.value = "San Salvador";
    } else {
        document.getElementById("modalTitle").innerText = "Editar Paciente";
    }
    
    modal.classList.remove("hidden");
    setTimeout(() => {
        modal.querySelector(".modal-backdrop").classList.remove("opacity-0");
        const panel = modal.querySelector(".modal-panel");
        panel.classList.remove("opacity-0", "translate-y-4", "sm:scale-95");
        panel.classList.add("opacity-100", "translate-y-0", "sm:scale-100");
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
        
        // Buscamos el nombre equivalente en la DB (o usamos el mismo si no está en el mapa)
        const dbKey = CAMPOS_MAP[input.name] || input.name;
        
        // Si el campo existe en el paciente, lo asignamos
        if(p[dbKey] !== undefined) {
            input.value = p[dbKey];
        }
    });

    // Casos especiales manuales (si los hay)
    form.id.value = p.id; // Asegurar ID
    if(p.fecha_nacimiento) form.fecha_nacimiento.value = p.fecha_nacimiento; // Asegurar fecha

    window.openModal("modalPacientes", "edit");
};

window.eliminarPaciente = function (id) {
    if (confirm("¿Eliminar expediente permanentemente?")) {
        pacientesDB = pacientesDB.filter((p) => p.id !== id);
        miPaginador.setData(pacientesDB);
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

// OPTIMIZADA: Guarda datos dinámicamente usando el mapa inverso
window.guardarDatos = function () {
    const form = document.getElementById("formPaciente");
    const formData = new FormData(form);
    const id = formData.get("id");
    
    // Convertir FormData a Objeto simple
    let datosForm = Object.fromEntries(formData.entries());

    // Validar requeridos
    if (!datosForm.nombre || !datosForm.apellido || !datosForm.telefono) {
        return alert("Por favor complete los campos obligatorios (*)");
    }

    // Traducir campos del Formulario -> DB usando el mapa
    Object.keys(CAMPOS_MAP).forEach(formKey => {
        if(datosForm[formKey] !== undefined) {
            datosForm[CAMPOS_MAP[formKey]] = datosForm[formKey];
            delete datosForm[formKey]; // Limpiar clave vieja si quieres, o dejarla
        }
    });

    datosForm.activo = parseInt(datosForm.activo);

    if (id) {
        const index = pacientesDB.findIndex((p) => p.id == id);
        if (index !== -1) {
            // Actualizar manteniendo datos extra (citas, etc)
            pacientesDB[index] = { ...pacientesDB[index], ...datosForm, id: parseInt(id) };
        }
    } else {
        // Crear Nuevo
        pacientesDB.push({ ...datosForm, id: Date.now(), citas: [], tratamientos: [] });
    }

    alert("Datos guardados correctamente");
    window.closeModal("modalPacientes");
    miPaginador.setData(pacientesDB);
};