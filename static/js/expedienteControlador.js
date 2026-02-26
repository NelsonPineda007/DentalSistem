// static/js/expedienteControlador.js

let pagosPaginador;

document.addEventListener("DOMContentLoaded", () => {
    cargarDatosPacienteDesdeURL(); // NUEVO: Simula la carga del backend
    
    configurarTabsExpediente();
    renderizarOdontogramaDiagnostico();
    renderizarOdontogramaOperatoria();
    
    if (typeof PaginadorTabla !== "undefined") {
        inicializarPaginadorPagos();
    }
});

// --- 0. LÓGICA DE BACKEND (FETCH SIMULADO) ---
function cargarDatosPacienteDesdeURL() {
    // 1. Obtenemos el ID de la URL (ej: expediente.php?id=2)
    const urlParams = new URLSearchParams(window.location.search);
    const idPaciente = urlParams.get('id');

    // Si no hay ID, no hacemos nada (el diseño por defecto cargará)
    if (!idPaciente) return;

    // 2. Simulación de la respuesta JSON que te dará PHP
    // En el futuro cambiarás esto por: await fetch(`backend/pacientes.php?id=${idPaciente}`)
    const baseDatosSimulada = [
        { id: 1, expediente: "2024-001", nombre: "María", apellido: "González", telefono: "7777-8888", edad: "34 años", alergias: "Penicilina" },
        { id: 2, expediente: "2024-002", nombre: "Carlos", apellido: "Martínez", telefono: "6666-7777", edad: "41 años", alergias: "" },
        { id: 3, expediente: "2024-003", nombre: "Ana", apellido: "Rodríguez", telefono: "7888-9999", edad: "31 años", alergias: "Látex" }
    ];

    const paciente = baseDatosSimulada.find(p => p.id == idPaciente);

    // 3. Inyectamos los datos en la cabecera del HTML
    if (paciente) {
        document.getElementById("exp-nombre").textContent = `${paciente.nombre} ${paciente.apellido}`;
        document.getElementById("exp-iniciales").textContent = `${paciente.nombre.charAt(0)}${paciente.apellido.charAt(0)}`;
        document.getElementById("exp-numero").textContent = paciente.expediente;
        document.getElementById("exp-edad").textContent = paciente.edad;
        document.getElementById("exp-telefono").textContent = paciente.telefono;

        if (paciente.alergias !== "") {
            document.getElementById("exp-alergias").classList.remove("hidden");
            document.getElementById("exp-alergias").classList.add("flex");
            document.getElementById("exp-alergia-texto").textContent = paciente.alergias;
        } else {
            document.getElementById("exp-alergias").classList.add("hidden");
            document.getElementById("exp-alergias").classList.remove("flex");
        }
    }
}

// --- 1. LÓGICA DE PESTAÑAS (TABS) ---
function configurarTabsExpediente() {
    const tabs = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            tabs.forEach((t) => {
                t.classList.remove("border-blue-800", "text-blue-800");
                t.classList.add("border-transparent", "text-slate-500");
            });
            tab.classList.remove("border-transparent", "text-slate-500");
            tab.classList.add("border-blue-800", "text-blue-800");

            contents.forEach((c) => c.classList.add("hidden"));
            document.getElementById(tab.dataset.target).classList.remove("hidden");

            if (tab.dataset.target === "tab-finanzas" && typeof pagosPaginador !== "undefined") {
                setTimeout(() => {
                    const scrollContainer = pagosPaginador.tbody ? pagosPaginador.tbody.parentElement.parentElement : null;
                    if(scrollContainer) pagosPaginador.recalcularYRenderizar(scrollContainer);
                }, 50);
            }
        });
    });
}

// --- 2. LÓGICA DE ODONTOGRAMAS ---
const dientesC1 = [18, 17, 16, 15, 14, 13, 12, 11];
const dientesC2 = [21, 22, 23, 24, 25, 26, 27, 28];
const dientesC3 = [31, 32, 33, 34, 35, 36, 37, 38];
const dientesC4 = [48, 47, 46, 45, 44, 43, 42, 41];

function obtenerSvgAnatomico(numero) {
    const num = parseInt(numero);
    if ([16,17,18, 26,27,28, 36,37,38, 46,47,48].includes(num)) {
        return `<path d="M6 3a3 3 0 0 0-3 3c0 2 1 4 2 5v5c0 1.5 1 2.5 2.5 2.5s2.5-1 2.5-2.5v-2h4v2c0 1.5 1 2.5 2.5 2.5s2.5-1 2.5-2.5v-5c1-1 2-3 2-5a3 3 0 0 0-3-3H6z" fill="currentColor" stroke="#94a3b8" stroke-width="1.5"/>`;
    }
    return `<path d="M8.5 3a2.5 2.5 0 0 0-2.5 2.5c0 3.5 1.5 6.5 2 10.5.3 2 1 4 4 4s3.7-2 4-4c.5-4 2-7 2-10.5A2.5 2.5 0 0 0 15.5 3h-7z" fill="currentColor" stroke="#94a3b8" stroke-width="1.5"/>`;
}

function crearDienteAnatomico(numero) {
    return `
        <div class="flex flex-col items-center gap-1 group cursor-pointer" onclick="toggleColorDienteAnatomico(this)">
            <span class="text-[10px] font-bold text-slate-400 group-hover:text-blue-800 transition-colors">${numero}</span>
            <div class="relative w-8 h-8">
                <svg class="w-full h-full diente-icon text-white drop-shadow-sm transition-colors" viewBox="0 0 24 24">
                     ${obtenerSvgAnatomico(numero)}
                </svg>
                <svg class="hidden marca-extraido absolute inset-0 w-full h-full text-slate-800 drop-shadow-md" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="4" y1="4" x2="20" y2="20"></line><line x1="20" y1="4" x2="4" y2="20"></line>
                </svg>
            </div>
        </div>
    `;
}

// --- LÓGICA DE CLIC EN DIENTES ANATÓMICOS (DIAGNÓSTICO) ---
window.toggleColorDienteAnatomico = function(elementoDOM) {
    const icon = elementoDOM.querySelector('.diente-icon path');
    const marca = elementoDOM.querySelector('.marca-extraido');
    
    // Leemos en qué estado está guardado (por defecto es 'sano')
    let estadoActual = elementoDOM.getAttribute('data-estado') || 'sano';

    if(estadoActual === 'sano') {
        elementoDOM.setAttribute('data-estado', 'caries');
        icon.setAttribute('fill', '#f43f5e'); // Caries (Rojo)
        marca.classList.add('hidden');
        
    } else if(estadoActual === 'caries') {
        elementoDOM.setAttribute('data-estado', 'restaurado');
        icon.setAttribute('fill', '#3b82f6'); // Restaurado (Azul)
        
    } else if(estadoActual === 'restaurado') {
        elementoDOM.setAttribute('data-estado', 'ausente');
        icon.setAttribute('fill', '#ffffff'); // Fondo blanco para la X
        marca.classList.remove('hidden');     // Ausente (Muestra la X)
        
    } else {
        // Vuelve a Sano perfectamente
        elementoDOM.setAttribute('data-estado', 'sano');
        icon.setAttribute('fill', 'currentColor'); // Vuelve al color original
        marca.classList.add('hidden');             // Oculta la X
    }
};


function crearDienteCircular(numero) {
    return `
        <div class="flex flex-col items-center gap-1">
            <span class="text-[10px] font-bold text-slate-400">${numero}</span>
            <svg width="32" height="32" viewBox="0 0 40 40" class="cursor-pointer rounded-full border border-slate-300 bg-white shadow-sm overflow-hidden">
                <polygon points="0,0 40,0 20,20" fill="#ffffff" stroke="#cbd5e1" stroke-width="1" class="cara-circulo hover:fill-slate-100 transition-colors" />
                <polygon points="40,0 40,40 20,20" fill="#ffffff" stroke="#cbd5e1" stroke-width="1" class="cara-circulo hover:fill-slate-100 transition-colors" />
                <polygon points="40,40 0,40 20,20" fill="#ffffff" stroke="#cbd5e1" stroke-width="1" class="cara-circulo hover:fill-slate-100 transition-colors" />
                <polygon points="0,40 0,0 20,20" fill="#ffffff" stroke="#cbd5e1" stroke-width="1" class="cara-circulo hover:fill-slate-100 transition-colors" />
                <circle cx="20" cy="20" r="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5" class="cara-circulo hover:fill-slate-100 transition-colors" />
            </svg>
        </div>
    `;
}

function renderizarOdontogramaDiagnostico() {
    if(!document.getElementById("diag-c1")) return;
    document.getElementById("diag-c1").innerHTML = dientesC1.map(num => crearDienteAnatomico(num)).join('');
    document.getElementById("diag-c2").innerHTML = dientesC2.map(num => crearDienteAnatomico(num)).join('');
    document.getElementById("diag-c3").innerHTML = dientesC3.map(num => crearDienteAnatomico(num)).join('');
    document.getElementById("diag-c4").innerHTML = dientesC4.map(num => crearDienteAnatomico(num)).join('');
}

// --- LÓGICA DE CLIC EN CÍRCULOS (OPERATORIA) ---
function renderizarOdontogramaOperatoria() {
    if(!document.getElementById("oper-c1")) return;
    document.getElementById("oper-c1").innerHTML = dientesC1.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c2").innerHTML = dientesC2.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c3").innerHTML = dientesC3.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c4").innerHTML = dientesC4.map(num => crearDienteCircular(num)).join('');

    // Ciclo de colores exacto y sin fallos
    document.querySelectorAll('.cara-circulo').forEach(cara => {
        cara.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Leemos el estado de esta rebanada en específico
            let estadoActual = this.getAttribute('data-estado') || 'sano';
            
            if(estadoActual === 'sano') {
                this.setAttribute('data-estado', 'caries');
                this.setAttribute('fill', '#f43f5e'); // Caries
                
            } else if(estadoActual === 'caries') {
                this.setAttribute('data-estado', 'restaurado');
                this.setAttribute('fill', '#3b82f6'); // Restaurado
                
            } else if(estadoActual === 'restaurado') {
                this.setAttribute('data-estado', 'ausente');
                this.setAttribute('fill', '#1e293b'); // Extraído
                
            } else {
                // Vuelve a Sano
                this.setAttribute('data-estado', 'sano');
                this.setAttribute('fill', '#ffffff'); // Sano (Blanco)
            }
        });
    });
}

// --- 3. LÓGICA DE LA TABLA Y MODALES ---

let pagosDB = [
    { id: 1, fecha: "2024-02-15", tratamiento: "Resina Dental", diente: "16", valor: 50.00, abono: 50.00, saldo: 0.00 },
    { id: 2, fecha: "2024-01-10", tratamiento: "Limpieza Profunda", diente: "General", valor: 35.00, abono: 20.00, saldo: 15.00 }
];

function inicializarPaginadorPagos() {
    pagosPaginador = new PaginadorTabla(pagosDB, 'auto', {
        tableBodyId: "pagosTableBody",
        containerId: "pagosTableContainer",
        renderRow: (p) => {
            const saldoColor = p.saldo > 0 ? "text-rose-500 font-bold" : "text-slate-400 font-bold";
            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-medium text-slate-600">${p.fecha}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-blue-800">${p.tratamiento}</span>
                            <span class="text-xs text-slate-400 font-medium">Pieza: ${p.diente}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">$${parseFloat(p.valor).toFixed(2)}</td>
                    <td class="px-6 py-4 font-bold text-emerald-600">$${parseFloat(p.abono).toFixed(2)}</td>
                    <td class="px-6 py-4 ${saldoColor}">$${parseFloat(p.saldo).toFixed(2)}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="window.abrirModalEdicion(${p.id})" class="text-emerald-500 hover:text-emerald-700 transition-colors p-1" title="Editar"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg></button>
                            <button type="button" onclick="window.eliminarVisita(${p.id})" class="text-rose-400 hover:text-rose-600 transition-colors p-1" title="Eliminar"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button>
                        </div>
                    </td>
                </tr>`;
        }
    });
}

window.openModal = function (modalID, mode = "add") {
    const modal = document.getElementById(modalID);
    const form = document.getElementById("formVisita");

    if (mode === "add") {
        document.getElementById("modalTitle").innerText = "Registrar Nueva Visita";
        form.reset();
        form.id.value = "";
    } else {
        document.getElementById("modalTitle").innerText = "Editar Visita";
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
    const v = pagosDB.find((x) => x.id === id);
    if (!v) return;
    const form = document.getElementById("formVisita");
    
    form.id.value = v.id;
    form.fecha.value = v.fecha;
    // Esto es temporal por la BD falsa. Al conectar PHP leerá el ID real
    document.getElementById("tratamiento_hidden").value = v.tratamiento;
    document.getElementById("tratamiento_search").value = v.tratamiento;    
    form.diente.value = v.diente;
    form.valor.value = v.valor;
    form.abono.value = v.abono;

    window.openModal("modalVisita", "edit");
};

window.eliminarVisita = function (id) {
    if (confirm("¿Eliminar este registro permanentemente?")) {
        pagosDB = pagosDB.filter(v => v.id !== id);
        pagosPaginador.setData(pagosDB);
    }
};

window.guardarDatos = function () {
    const form = document.getElementById("formVisita");
    const id = form.id.value;
    
    const valor = parseFloat(form.valor.value);
    const abono = parseFloat(form.abono.value);
    
    const nuevaVisita = {
        id: id ? parseInt(id) : Date.now(),
        fecha: form.fecha.value,
        tratamiento: form.tratamiento.options[form.tratamiento.selectedIndex].text,
        diente: form.diente.value || "General",
        valor: valor,
        abono: abono,
        saldo: valor - abono
    };

    if (id) {
        const index = pagosDB.findIndex((v) => v.id == id);
        pagosDB[index] = nuevaVisita;
    } else {
        pagosDB.unshift(nuevaVisita);
    }

    window.closeModal("modalVisita");
    pagosPaginador.setData(pagosDB);
};

// --- 4. LÓGICA DEL BUSCADOR INTELIGENTE DE TRATAMIENTOS ---

// Simulamos tu tabla 'tratamientos' de la Base de Datos
const catalogoTratamientos = [
    { id: 1, codigo: "LMP-01", nombre: "Limpieza Profunda (Profilaxis)", precio: 35.00 },
    { id: 2, codigo: "RST-01", nombre: "Resina Dental Simple", precio: 50.00 },
    { id: 3, codigo: "RST-02", nombre: "Resina Dental Compuesta", precio: 65.00 },
    { id: 4, codigo: "EXT-01", nombre: "Extracción Simple", precio: 45.00 },
    { id: 5, codigo: "EXT-02", nombre: "Extracción Cordal (Cirugía)", precio: 120.00 },
    { id: 6, codigo: "END-01", nombre: "Endodoncia Unirradicular", precio: 150.00 },
    { id: 7, codigo: "ORT-01", nombre: "Control de Ortodoncia Mensual", precio: 40.00 }
];

const inputSearch = document.getElementById('tratamiento_search');
const inputHidden = document.getElementById('tratamiento_hidden');
const dropdown = document.getElementById('tratamiento_dropdown');
const inputValor = document.querySelector('input[name="valor"]');

// Función para renderizar la lista
function renderizarResultadosBuscador(filtro = "") {
    dropdown.innerHTML = "";
    const texto = filtro.toLowerCase();
    
    const filtrados = catalogoTratamientos.filter(t => 
        t.nombre.toLowerCase().includes(texto) || t.codigo.toLowerCase().includes(texto)
    );

    if (filtrados.length === 0) {
        dropdown.innerHTML = `<div class="p-4 text-center text-sm text-slate-400">No se encontraron tratamientos</div>`;
        return;
    }

    filtrados.forEach(t => {
        const item = document.createElement("div");
        item.className = "p-3 hover:bg-blue-50 cursor-pointer transition-colors flex justify-between items-center group";
        // Estructura de mini-tabla en cada fila
        item.innerHTML = `
            <div>
                <div class="font-bold text-sm text-slate-700 group-hover:text-blue-800">${t.nombre}</div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Código: ${t.codigo}</div>
            </div>
            <div class="font-bold text-emerald-600 text-sm bg-emerald-50 px-2 py-1 rounded-lg">
                $${t.precio.toFixed(2)}
            </div>
        `;
        
        // Al hacer clic en un tratamiento
        item.addEventListener("click", () => {
            inputSearch.value = t.nombre; // Muestra el nombre en el buscador
            inputHidden.value = t.nombre; // Guarda el valor real para el formulario
            inputValor.value = t.precio.toFixed(2); // ¡AUTO-RELLENA EL PRECIO!
            dropdown.classList.add("hidden");
        });
        
        dropdown.appendChild(item);
    });
}

// Escuchadores de eventos
inputSearch.addEventListener("focus", () => {
    renderizarResultadosBuscador(inputSearch.value);
    dropdown.classList.remove("hidden");
});

inputSearch.addEventListener("input", (e) => {
    renderizarResultadosBuscador(e.target.value);
});

// Cierra el dropdown si haces clic afuera
document.addEventListener("click", (e) => {
    if (!inputSearch.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add("hidden");
    }
});