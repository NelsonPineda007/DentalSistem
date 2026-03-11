// public/js/expedienteControlador.js

// =========================================================================
// VARIABLES GLOBALES
// =========================================================================
let pagosPaginador;
let consultasPaginador;
let consultasDB = [];

document.addEventListener("DOMContentLoaded", () => {
    cargarDatosPacienteDesdeURL(); 
    configurarTabsExpediente();
    renderizarOdontogramaDiagnostico();
    renderizarOdontogramaOperatoria();
    
    if (typeof PaginadorTabla !== "undefined") {
        inicializarPaginadorPagos();
        inicializarPaginadorConsultas();
    }
});

// =========================================================================
// 0. LÓGICA DE BACKEND (FETCH REAL A LARAVEL Y CARGA DE DATOS)
// =========================================================================
async function cargarDatosPacienteDesdeURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const idPaciente = urlParams.get('id');

    if (!idPaciente) return;

    try {
        const respuesta = await fetch(`/api/pacientes/${idPaciente}`);
        if (!respuesta.ok) throw new Error('No se pudo encontrar el paciente.');

        const paciente = await respuesta.json();

        document.getElementById("exp-nombre").textContent = `${paciente.nombre} ${paciente.apellido}`;
        document.getElementById("exp-iniciales").textContent = `${paciente.nombre.charAt(0)}${paciente.apellido.charAt(0)}`;
        document.getElementById("exp-numero").textContent = paciente.numero_expediente;
        
        if(paciente.fecha_nacimiento) {
            const hoy = new Date();
            const nacimiento = new Date(paciente.fecha_nacimiento);
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const m = hoy.getMonth() - nacimiento.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) edad--;
            document.getElementById("exp-edad").textContent = `${edad} años`;
        } else {
            document.getElementById("exp-edad").textContent = "Sin fecha nac.";
        }
        
        document.getElementById("exp-telefono").textContent = paciente.telefono || "Sin teléfono";

        if (paciente.alergias && paciente.alergias.trim() !== "") {
            document.getElementById("exp-alergias").classList.remove("hidden");
            document.getElementById("exp-alergias").classList.add("flex");
            document.getElementById("exp-alergia-texto").textContent = paciente.alergias;
        } else {
            document.getElementById("exp-alergias").classList.add("hidden");
            document.getElementById("exp-alergias").classList.remove("flex");
        }

        try {
            const resFicha = await fetch(`/api/expediente/${idPaciente}`);
            if (resFicha.ok) {
                const ficha = await resFicha.json();
                
                if (ficha.odontograma) {
                    if (ficha.odontograma.diagnostico) {
                        Object.entries(ficha.odontograma.diagnostico).forEach(([numero, estado]) => {
                            const diente = document.querySelector(`#tab-odontograma div[data-numero="${numero}"]`);
                            if (diente) {
                                let estadoActual = diente.getAttribute('data-estado') || 'sano';
                                while(estadoActual !== estado) {
                                    window.toggleColorDienteAnatomico(diente);
                                    estadoActual = diente.getAttribute('data-estado');
                                }
                            }
                        });
                    }

                    if (ficha.odontograma.operatoria) {
                        Object.entries(ficha.odontograma.operatoria).forEach(([numero, carasCargadas]) => {
                            const dienteOper = document.querySelector(`#oper-c1 [data-numero="${numero}"], #oper-c2 [data-numero="${numero}"], #oper-c3 [data-numero="${numero}"], #oper-c4 [data-numero="${numero}"]`);
                            if (dienteOper) {
                                const carasDOM = dienteOper.querySelectorAll('.cara-circulo');
                                carasDOM.forEach((cara, index) => {
                                    const estadoGuardado = carasCargadas[index];
                                    if (estadoGuardado !== 'sano') {
                                        cara.setAttribute('data-estado', estadoGuardado);
                                        if (estadoGuardado === 'caries') cara.setAttribute('fill', '#f43f5e');
                                        else if (estadoGuardado === 'restaurado') cara.setAttribute('fill', '#3b82f6');
                                        else if (estadoGuardado === 'ausente') cara.setAttribute('fill', '#1e293b');
                                    }
                                });
                            }
                        });
                    }

                    if (ficha.odontograma.detalles_extra) {
                        const extra = ficha.odontograma.detalles_extra;
                        if(document.getElementById('prot_color')) document.getElementById('prot_color').value = extra.prot_color || '';
                        if(document.getElementById('prot_guia')) document.getElementById('prot_guia').value = extra.prot_guia || '';
                        if(document.getElementById('prot_molde')) document.getElementById('prot_molde').value = extra.prot_molde || '';
                        if(document.getElementById('prot_acrilico')) document.getElementById('prot_acrilico').checked = extra.prot_acrilico || false;
                        if(document.getElementById('prot_porcelana')) document.getElementById('prot_porcelana').checked = extra.prot_porcelana || false;
                        if(document.getElementById('endo_diente')) document.getElementById('endo_diente').value = extra.endo_diente || '';
                        if(document.getElementById('endo_vitalidad')) document.getElementById('endo_vitalidad').value = extra.endo_vitalidad || '';
                        if(document.getElementById('endo_provisional')) document.getElementById('endo_provisional').value = extra.endo_provisional || '';
                        if(document.getElementById('endo_trabajo')) document.getElementById('endo_trabajo').value = extra.endo_trabajo || '';
                    }
                }

                if (ficha.consultas) {
                    consultasDB = ficha.consultas;
                    if(consultasPaginador) consultasPaginador.setData(consultasDB);
                }
            }
        } catch (errorFicha) {
            console.error("Error al cargar el odontograma:", errorFicha);
        }

    } catch (error) {
        console.error("Error cargando paciente:", error);
        document.getElementById("exp-nombre").textContent = "Error al cargar paciente";
    }
}

// =========================================================================
// 1. LÓGICA DE PESTAÑAS (TABS)
// =========================================================================
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
                setTimeout(() => { if(pagosPaginador.tbody) pagosPaginador.recalcularYRenderizar(pagosPaginador.tbody.parentElement.parentElement); }, 50);
            }
            if (tab.dataset.target === "tab-consultas" && typeof consultasPaginador !== "undefined") {
                setTimeout(() => { if(consultasPaginador.tbody) consultasPaginador.recalcularYRenderizar(consultasPaginador.tbody.parentElement.parentElement); }, 50);
            }
        });
    });
}

// =========================================================================
// 2. LÓGICA DE HISTORIAL DE CONSULTAS
// =========================================================================
function inicializarPaginadorConsultas() {
    if (!document.getElementById("consultasTableContainer")) return;
    
    consultasPaginador = new PaginadorTabla(consultasDB, 'auto', {
        tableBodyId: "consultasTableBody",
        containerId: "consultasTableContainer",
        renderRow: (c) => {
            const fecha = c.fecha_consulta ? new Date(c.fecha_consulta).toLocaleDateString() : '-';
            const proxima = c.proxima_cita_recomendada ? new Date(c.proxima_cita_recomendada).toLocaleDateString() : 'No asignada';
            const resumen = c.sintomas || c.observaciones || 'Sin detalles adicionales registrados.';

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-slate-600 whitespace-nowrap">${fecha}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col justify-center">
                            <span class="font-bold text-blue-800 text-sm truncate max-w-[250px]">${c.motivo_consulta || 'Consulta de control'}</span>
                            <span class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[250px]" title="${resumen}">${resumen}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <div class="truncate max-w-[200px]" title="${c.diagnostico || '-'}">${c.diagnostico || '-'}</div>
                    </td>
                    <td class="px-6 py-4 font-medium text-emerald-600 whitespace-nowrap">${proxima}</td>
                    <td class="px-6 py-4">
                        <button type="button" onclick="window.editarConsulta(${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Ver / Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </td>
                </tr>`;
        }
    });
}

window.editarConsulta = function(id) {
    const c = consultasDB.find(x => x.id === id);
    if (!c) return;

    document.getElementById('hc_consulta_id').value = c.id;
    document.getElementById('hc_motivo').value = c.motivo_consulta || '';
    document.getElementById('hc_sintomas').value = c.sintomas || '';
    document.getElementById('hc_observaciones').value = c.observaciones || '';
    document.getElementById('hc_diagnostico').value = c.diagnostico || '';
    document.getElementById('hc_prescripciones').value = c.prescripciones || '';
    
    if(c.proxima_cita_recomendada) {
        document.getElementById('hc_proxima_cita').value = c.proxima_cita_recomendada.split(' ')[0];
    } else {
        document.getElementById('hc_proxima_cita').value = '';
    }

    document.querySelector('.tab-btn[data-target="tab-historia"]').click();
    Alerta.info("Modo Edición", "Estás viendo una consulta pasada.");
};

window.nuevaConsulta = function() {
    document.getElementById('hc_consulta_id').value = '';
    document.getElementById('hc_motivo').value = '';
    document.getElementById('hc_sintomas').value = '';
    document.getElementById('hc_observaciones').value = '';
    document.getElementById('hc_diagnostico').value = '';
    document.getElementById('hc_prescripciones').value = '';
    document.getElementById('hc_proxima_cita').value = '';
    
    document.querySelector('.tab-btn[data-target="tab-historia"]').click();
};

// =========================================================================
// 3. LÓGICA DE ODONTOGRAMAS
// =========================================================================
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
        <div class="flex flex-col items-center gap-1 group cursor-pointer" onclick="toggleColorDienteAnatomico(this)" data-numero="${numero}">
            <span class="text-[10px] font-bold text-slate-400 group-hover:text-blue-800 transition-colors">${numero}</span>
            <div class="relative w-8 h-8">
                <svg class="w-full h-full diente-icon text-white drop-shadow-sm transition-colors" viewBox="0 0 24 24">
                     ${obtenerSvgAnatomico(numero)}
                </svg>
                <svg class="hidden marca-extraido absolute inset-0 w-full h-full drop-shadow-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                    <line x1="4" y1="4" x2="20" y2="20"></line><line x1="20" y1="4" x2="4" y2="20"></line>
                </svg>
            </div>
        </div>
    `;
}

window.toggleColorDienteAnatomico = function(elementoDOM) {
    const icon = elementoDOM.querySelector('.diente-icon path');
    const marca = elementoDOM.querySelector('.marca-extraido');
    let estadoActual = elementoDOM.getAttribute('data-estado') || 'sano';

    if(estadoActual === 'sano') {
        elementoDOM.setAttribute('data-estado', 'caries');
        icon.setAttribute('fill', '#f43f5e'); 
        marca.classList.add('hidden');
    } else if(estadoActual === 'caries') {
        elementoDOM.setAttribute('data-estado', 'restaurado');
        icon.setAttribute('fill', '#3b82f6'); 
    } else if(estadoActual === 'restaurado') {
        elementoDOM.setAttribute('data-estado', 'ausente');
        icon.setAttribute('fill', '#ffffff'); 
        marca.classList.remove('hidden', 'text-rose-500'); 
        marca.classList.add('text-slate-800'); 
    } else if(estadoActual === 'ausente') {
        elementoDOM.setAttribute('data-estado', 'extraccion');
        icon.setAttribute('fill', '#ffffff'); 
        marca.classList.remove('hidden', 'text-slate-800'); 
        marca.classList.add('text-rose-500'); 
    } else {
        elementoDOM.setAttribute('data-estado', 'sano');
        icon.setAttribute('fill', 'currentColor'); 
        marca.classList.add('hidden'); 
    }

    actualizarTextosDiagnostico();
};

function actualizarTextosDiagnostico() {
    let ausentes = [];
    let extracciones = [];
    let caries = [];

    document.querySelectorAll('#diag-c1 [data-numero], #diag-c2 [data-numero], #diag-c3 [data-numero], #diag-c4 [data-numero]').forEach(el => {
        const numero = el.getAttribute('data-numero');
        const estado = el.getAttribute('data-estado');
        if (estado === 'ausente') ausentes.push(numero);
        if (estado === 'extraccion') extracciones.push(numero);
        if (estado === 'caries') caries.push(numero);
    });

    const inputAusentes = document.getElementById('diag_ausentes');
    const inputExtraccion = document.getElementById('diag_extraccion');
    const inputFinal = document.getElementById('diag_final');

    if (inputAusentes) inputAusentes.value = ausentes.length > 0 ? ausentes.join(', ') : '';
    if (inputExtraccion) inputExtraccion.value = extracciones.length > 0 ? `Pieza(s): ${extracciones.join(', ')}` : '';
    if (inputFinal) {
        if (caries.length > 0) inputFinal.value = `Se detectan caries en piezas: ${caries.join(', ')}`;
        else if (inputFinal.value.includes('caries en piezas')) inputFinal.value = ""; 
    }
}

function crearDienteCircular(numero) {
    return `
        <div class="flex flex-col items-center gap-1" data-numero="${numero}">
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

function renderizarOdontogramaOperatoria() {
    if(!document.getElementById("oper-c1")) return;
    document.getElementById("oper-c1").innerHTML = dientesC1.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c2").innerHTML = dientesC2.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c3").innerHTML = dientesC3.map(num => crearDienteCircular(num)).join('');
    document.getElementById("oper-c4").innerHTML = dientesC4.map(num => crearDienteCircular(num)).join('');

    document.querySelectorAll('.cara-circulo').forEach(cara => {
        cara.addEventListener('click', function(e) {
            e.stopPropagation();
            let estadoActual = this.getAttribute('data-estado') || 'sano';
            
            if(estadoActual === 'sano') {
                this.setAttribute('data-estado', 'caries');
                this.setAttribute('fill', '#f43f5e'); 
            } else if(estadoActual === 'caries') {
                this.setAttribute('data-estado', 'restaurado');
                this.setAttribute('fill', '#3b82f6'); 
            } else if(estadoActual === 'restaurado') {
                this.setAttribute('data-estado', 'ausente');
                this.setAttribute('fill', '#1e293b'); 
            } else {
                this.setAttribute('data-estado', 'sano');
                this.setAttribute('fill', '#ffffff'); 
            }
        });
    });
}

// =========================================================================
// 4. LÓGICA DE FACTURACIÓN Y PAGOS (POS)
// =========================================================================

// DATOS DE PRUEBA (Mientras conectamos con tu BD real de tratamientos)
const catalogoTratamientos = [
    { id: 1, codigo: "LMP-01", nombre: "Limpieza Profunda (Profilaxis)", precio: 35.00 },
    { id: 2, codigo: "RST-01", nombre: "Resina Dental Simple", precio: 50.00 },
    { id: 3, codigo: "RST-02", nombre: "Resina Dental Compuesta", precio: 65.00 },
    { id: 4, codigo: "EXT-01", nombre: "Extracción Simple", precio: 45.00 },
    { id: 5, codigo: "EXT-02", nombre: "Extracción Cordal (Cirugía)", precio: 120.00 },
    { id: 6, codigo: "END-01", nombre: "Endodoncia Unirradicular", precio: 150.00 },
    { id: 7, codigo: "ORT-01", nombre: "Control de Ortodoncia Mensual", precio: 40.00 }
];

let tratamientosSeleccionados = [];
let pagosDB = []; 

// FUNCIÓN 1: DIBUJA LOS TRATAMIENTOS AGREGADOS Y CALCULA EL SUBTOTAL
function renderizarBadgesTratamientos() {
    const contenedor = document.getElementById('lista_tratamientos');
    if (!contenedor) return;
    contenedor.innerHTML = '';
    let subtotal = 0;

    if (tratamientosSeleccionados.length === 0) {
        contenedor.innerHTML = `<span class="text-xs text-slate-400 italic px-2">Ningún tratamiento agregado a la factura...</span>`;
    } else {
        tratamientosSeleccionados.forEach((t, index) => {
            subtotal += t.precio;
            const item = document.createElement('div');
            item.className = "flex justify-between items-center px-3 py-2 bg-white rounded-lg border border-slate-200 shadow-sm";
            item.innerHTML = `
                <div class="flex flex-col">
                    <span class="font-bold text-slate-700 text-xs">${t.nombre}</span>
                    <span class="text-[10px] text-slate-400 uppercase">${t.codigo || 'S/C'}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-blue-700 text-sm">$${t.precio.toFixed(2)}</span>
                    <button type="button" class="text-slate-300 hover:text-rose-500 transition-colors focus:outline-none" onclick="removerTratamiento(${index})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            contenedor.appendChild(item);
        });
    }

    // Actualizamos el subtotal en la pantalla
    const spanSubtotal = document.getElementById('resumen_subtotal');
    if(spanSubtotal) spanSubtotal.innerText = subtotal.toFixed(2);
    
    // Llamamos a la función que calcula el total y el saldo final
    calcularTotalesFactura();
}

// FUNCIÓN 2: HACE TODA LA MATEMÁTICA DE LA FACTURA EN TIEMPO REAL
window.calcularTotalesFactura = function() {
    const spanSubtotal = document.getElementById('resumen_subtotal');
    const inputDescuento = document.getElementById('input_descuento');
    const spanTotal = document.getElementById('resumen_total');
    const inputTotalOculto = document.getElementById('input_total_oculto');
    const inputAbono = document.getElementById('input_abono');
    const spanSaldo = document.getElementById('resumen_saldo');

    if(!spanSubtotal) return;

    let subtotal = parseFloat(spanSubtotal.innerText) || 0;
    let descuento = parseFloat(inputDescuento.value) || 0;
    
    // El descuento no puede ser mayor al subtotal
    if(descuento > subtotal) {
        descuento = subtotal;
        inputDescuento.value = descuento.toFixed(2);
    }

    let total = subtotal - descuento;
    spanTotal.innerText = total.toFixed(2);
    inputTotalOculto.value = total.toFixed(2);

    let abono = parseFloat(inputAbono.value) || 0;
    let saldo = total - abono;

    // Si abona más de la cuenta, lo ajustamos al total exacto
    if(saldo < 0) {
        saldo = 0;
        inputAbono.value = total.toFixed(2);
    }

    spanSaldo.innerText = saldo.toFixed(2);
    
    // Cambiar el color del Saldo: Verde si está pagado, Rojo si debe
    if(saldo === 0 && total > 0) {
        spanSaldo.parentElement.classList.replace('text-rose-500', 'text-emerald-500');
    } else {
        spanSaldo.parentElement.classList.replace('text-emerald-500', 'text-rose-500');
    }
}

// FUNCIÓN 3: ELIMINAR TRATAMIENTO DEL CARRITO
window.removerTratamiento = function(index) {
    tratamientosSeleccionados.splice(index, 1);
    renderizarBadgesTratamientos();
};

// BUSCADOR EN VIVO DE TRATAMIENTOS
const inputSearch = document.getElementById('tratamiento_search');
const dropdown = document.getElementById('tratamiento_dropdown');

function renderizarResultadosBuscador(filtro = "") {
    if(!dropdown) return;
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
        item.innerHTML = `
            <div>
                <div class="font-bold text-sm text-slate-700 group-hover:text-blue-800">${t.nombre}</div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Código: ${t.codigo}</div>
            </div>
            <div class="font-bold text-emerald-600 text-sm bg-emerald-50 px-2 py-1 rounded-lg">
                $${t.precio.toFixed(2)}
            </div>
        `;
        
        item.addEventListener("click", () => {
            tratamientosSeleccionados.push({ nombre: t.nombre, codigo: t.codigo, precio: t.precio });
            renderizarBadgesTratamientos();
            inputSearch.value = ""; 
            dropdown.classList.add("hidden");
        });
        
        dropdown.appendChild(item);
    });
}

if(inputSearch) {
    inputSearch.addEventListener("focus", () => {
        renderizarResultadosBuscador(inputSearch.value);
        dropdown.classList.remove("hidden");
    });
    inputSearch.addEventListener("input", (e) => {
        renderizarResultadosBuscador(e.target.value);
    });
    document.addEventListener("click", (e) => {
        if (!inputSearch.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add("hidden");
        }
    });
}

function inicializarPaginadorPagos() {
    if (!document.getElementById("pagosTableContainer")) return;
    
    pagosPaginador = new PaginadorTabla(pagosDB, 'auto', {
        tableBodyId: "pagosTableBody",
        containerId: "pagosTableContainer",
        renderRow: (p) => {
            const saldoColor = p.saldo > 0 ? "text-rose-500 font-bold" : "text-slate-400 font-bold";
            const estadoBadge = p.saldo > 0 
                ? `<span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Pendiente</span>` 
                : `<span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Pagado</span>`;

            const numFactura = `FAC-${String(p.id).padStart(4, '0')}`;

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors h-[75px]">
                    <td class="px-6 py-4 font-bold text-slate-600">${p.fecha}</td>
                    <td class="px-6 py-4 font-mono text-xs text-blue-800 font-bold">${numFactura}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm line-clamp-1">${p.tratamiento}</span>
                            <span class="text-[10px] text-slate-400">Pieza: ${p.diente || 'General'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-black text-slate-800">$${parseFloat(p.valor).toFixed(2)}</td>
                    <td class="px-6 py-4 ${saldoColor}">$${parseFloat(p.saldo).toFixed(2)}</td>
                    <td class="px-6 py-4">${estadoBadge}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="Alerta.info('Próximamente', 'Generador de Facturas en PDF en desarrollo.')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white transition-all shadow-sm border border-slate-200" title="Imprimir Factura">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </button>
                            <button type="button" onclick="window.abrirModalEdicion(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Editar / Abonar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            </button>
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
        document.getElementById("modalTitle").innerText = "Registrar Nueva Factura";
        form.reset();
        form.id.value = "";
        
        tratamientosSeleccionados = [];
        renderizarBadgesTratamientos();

        let dientesAfectados = new Set();
        document.querySelectorAll('#tab-odontograma [data-estado]').forEach(elemento => {
            const estado = elemento.getAttribute('data-estado');
            if (estado === 'caries' || estado === 'restaurado' || estado === 'extraccion') {
                const contenedorDiente = elemento.closest('[data-numero]');
                if(contenedorDiente) dientesAfectados.add(contenedorDiente.getAttribute('data-numero'));
            }
        });

        const inputDientes = document.getElementById('input_dientes_modal');
        if(inputDientes) {
            inputDientes.value = dientesAfectados.size > 0 ? Array.from(dientesAfectados).join(', ') : "";
        }
    } else {
        document.getElementById("modalTitle").innerText = "Abonar / Editar Factura";
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

// =========================================================================
// 5. GUARDADO REAL EN BASE DE DATOS (ODONTOGRAMA E HISTORIA)
// =========================================================================
window.guardarFichaClinica = async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const idPaciente = urlParams.get('id');

    if (!idPaciente) {
        return Alerta.error("Error de ID", "No se detectó el paciente actual.");
    }

    let odontogramaData = {
        diagnostico: {},
        operatoria: {},
        detalles_extra: {
            prot_color: document.getElementById('prot_color')?.value || '',
            prot_guia: document.getElementById('prot_guia')?.value || '',
            prot_molde: document.getElementById('prot_molde')?.value || '',
            prot_acrilico: document.getElementById('prot_acrilico')?.checked || false,
            prot_porcelana: document.getElementById('prot_porcelana')?.checked || false,
            endo_diente: document.getElementById('endo_diente')?.value || '',
            endo_vitalidad: document.getElementById('endo_vitalidad')?.value || '',
            endo_provisional: document.getElementById('endo_provisional')?.value || '',
            endo_trabajo: document.getElementById('endo_trabajo')?.value || ''
        }
    };

    document.querySelectorAll('#diag-c1 [data-numero], #diag-c2 [data-numero], #diag-c3 [data-numero], #diag-c4 [data-numero]').forEach(el => {
        const numero = el.getAttribute('data-numero');
        const estado = el.getAttribute('data-estado') || 'sano';
        if(estado !== 'sano') odontogramaData.diagnostico[numero] = estado;
    });

    document.querySelectorAll('#oper-c1 [data-numero], #oper-c2 [data-numero], #oper-c3 [data-numero], #oper-c4 [data-numero]').forEach(el => {
        const numero = el.getAttribute('data-numero');
        let caras = [];
        el.querySelectorAll('.cara-circulo').forEach(cara => {
            caras.push(cara.getAttribute('data-estado') || 'sano');
        });
        if(caras.some(c => c !== 'sano')) odontogramaData.operatoria[numero] = caras;
    });

    const historiaData = {
        consulta_id: document.getElementById('hc_consulta_id')?.value || '', 
        motivo_consulta: document.getElementById('hc_motivo')?.value || '',
        sintomas: document.getElementById('hc_sintomas')?.value || '',
        observaciones: document.getElementById('hc_observaciones')?.value || '',
        diagnostico: document.getElementById('hc_diagnostico')?.value || '',
        prescripciones: document.getElementById('hc_prescripciones')?.value || '',
        proxima_cita: document.getElementById('hc_proxima_cita')?.value || null,
    };

    const payload = {
        odontograma: odontogramaData,
        historia: historiaData
    };

    const btn = document.getElementById('btnGuardarFicha');
    const textoOriginal = btn.innerHTML;

    try {
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        btn.disabled = true;

        await API.post(`/api/expediente/${idPaciente}/guardar`, payload);

        Alerta.exito("¡Ficha Guardada!", "Odontograma y consulta registrados exitosamente.");

        document.getElementById('hc_consulta_id').value = '';
        document.getElementById('hc_motivo').value = '';
        document.getElementById('hc_sintomas').value = '';
        document.getElementById('hc_observaciones').value = '';
        document.getElementById('hc_diagnostico').value = '';
        document.getElementById('hc_prescripciones').value = '';
        document.getElementById('hc_proxima_cita').value = '';

        await cargarDatosPacienteDesdeURL();

    } catch (error) {
        console.error("Error al guardar ficha:", error);
        Alerta.error("Error del Servidor", "No se pudo guardar la información.");
    } finally {
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }
};

// =========================================================================
// 6. GENERACIÓN DE PDF PERFECTO (Ficha Clínica)
// =========================================================================
window.imprimirFichaPDF = async function() {
    const elNombre = document.getElementById("exp-nombre");
    const nombrePaciente = elNombre ? elNombre.innerText : "Paciente";
    
    if(typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
        return Alerta.advertencia("Cargando...", "Espere un momento a que el creador de PDF termine de cargar.");
    }

    const btn = document.querySelector('button[onclick="window.imprimirFichaPDF()"]');
    const btnText = btn ? btn.innerHTML : "";
    if(btn) btn.innerHTML = `<svg class="w-4 h-4 animate-spin inline mr-2" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generando PDF...`;

    const contenedorImpresion = document.getElementById('contenedor-impresion');

    try {
        const canvas = await html2canvas(contenedorImpresion, { 
            scale: 2, 
            backgroundColor: '#ffffff',
            windowWidth: 1200,
            onclone: function(clonedDoc) {
                const clonedContainer = clonedDoc.getElementById('contenedor-impresion');
                clonedContainer.style.width = '1200px';
                clonedContainer.style.maxWidth = '1200px';
                clonedContainer.style.height = 'max-content';
                clonedContainer.style.overflow = 'visible';

                const tabOdon = clonedDoc.getElementById('tab-odontograma');
                const tabHist = clonedDoc.getElementById('tab-historia');
                const tabCons = clonedDoc.getElementById('tab-consultas');
                const tabFin = clonedDoc.getElementById('tab-finanzas');

                if(tabOdon) { tabOdon.classList.remove('hidden'); tabOdon.style.display = 'flex'; }
                if(tabHist) { tabHist.classList.remove('hidden', 'h-full'); tabHist.style.display = 'block'; tabHist.style.height = 'max-content'; }
                if(tabCons) tabCons.style.display = 'none';
                if(tabFin) tabFin.style.display = 'none';

                const inputs = clonedContainer.querySelectorAll('input[type="text"], input[type="number"], input[type="date"]');
                inputs.forEach(input => {
                    const originalInput = document.getElementById(input.id);
                    const div = clonedDoc.createElement('div');
                    div.className = input.className + " flex items-center"; 
                    div.innerText = originalInput ? originalInput.value : input.value;
                    input.parentNode.replaceChild(div, input);
                });

                const textareas = clonedContainer.querySelectorAll('textarea');
                textareas.forEach(textarea => {
                    const originalTextarea = document.getElementById(textarea.id);
                    const div = clonedDoc.createElement('div');
                    div.className = textarea.className;
                    div.style.height = 'auto';
                    div.style.minHeight = '60px';
                    div.style.whiteSpace = 'pre-wrap';
                    div.innerText = originalTextarea ? originalTextarea.value : textarea.value;
                    textarea.parentNode.replaceChild(div, textarea);
                });

                const checkboxes = clonedContainer.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    const originalCb = document.getElementById(cb.id);
                    const div = clonedDoc.createElement('div');
                    div.className = 'w-4 h-4 border border-slate-300 rounded flex items-center justify-center text-blue-800';
                    if(originalCb && originalCb.checked) {
                        div.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                    }
                    cb.parentNode.replaceChild(div, cb);
                });
            }
        });
        
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        
        const pdf = new jsPDF('l', 'mm', 'a4'); 
        pdf.setFontSize(16);
        pdf.setTextColor(30, 64, 175);
        pdf.text(`Ficha Técnica Dental y Médica - ${nombrePaciente}`, 15, 15);
        
        pdf.setFontSize(10);
        pdf.setTextColor(100, 116, 139);
        pdf.text(`Fecha de Impresión: ${new Date().toLocaleDateString()}`, 15, 22);

        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const margin = 15;
        const topMargin = 28; 

        const imgProps = pdf.getImageProperties(imgData);
        let finalWidth = pageWidth - (margin * 2);
        let finalHeight = (imgProps.height * finalWidth) / imgProps.width;

        let heightLeft = finalHeight;
        let position = topMargin;

        pdf.addImage(imgData, 'PNG', margin, position, finalWidth, finalHeight);
        heightLeft -= (pageHeight - topMargin);

        while (heightLeft > 0) {
            pdf.addPage();
            let amountShown = finalHeight - heightLeft;
            position = 15 - amountShown; 
            pdf.addImage(imgData, 'PNG', margin, position, finalWidth, finalHeight);
            heightLeft -= (pageHeight - 15);
        }

        pdf.save(`Ficha_Dental_${nombrePaciente.replace(/ /g, '_')}.pdf`);
        
    } catch (error) {
        console.error("Error al generar PDF:", error);
        Alerta.error("Error", "Hubo un error al crear el PDF. Revise la consola.");
    } finally {
        if(btn) btn.innerHTML = btnText;
    }
};