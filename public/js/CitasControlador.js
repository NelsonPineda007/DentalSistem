// public/js/CitasControlador.js

let citasDB = []; 
let miPaginadorCitas; 
let pacientesGlobal = []; 
let autoRefreshInterval; 

(function fixAlertButtons() {
    if (document.getElementById('fix-alert-buttons')) return;
    const style = document.createElement('style');
    style.id = 'fix-alert-buttons';
    style.innerHTML = `
        .da-modal .swal2-actions .da-btn {
            flex: 1 1 0px !important; 
            padding: 12px 4px !important;
            font-size: 0.85rem !important; 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            min-height: 48px !important;
            line-height: 1.2 !important;
        }
        @media (max-width: 480px) {
            .da-modal .swal2-actions { flex-direction: column !important; }
            .da-modal .swal2-actions .da-btn { width: 100% !important; flex: none !important; }
        }
    `;
    document.head.appendChild(style);
})();

document.addEventListener('DOMContentLoaded', () => {
    if (typeof PaginadorTabla === 'undefined') {
        window.Alerta.error("Error del sistema", "paginadorTabla.js no cargado.");
        return;
    }

    configurarFiltros();
    cargarCitasDesdeBD();
    configurarBuscadorPacientes();

    autoRefreshInterval = setInterval(() => {
        cargarCitasSilencioso();
    }, 15000); 
});

window.formatearHora = function(input) {
    let val = input.value.replace(/\D/g, ''); 
    if (val.length > 4) val = val.substring(0, 4);

    if (val.length === 3) {
        input.value = val.substring(0, 1) + ':' + val.substring(1, 3);
    } else if (val.length === 4) {
        input.value = val.substring(0, 2) + ':' + val.substring(2, 4);
    } else {
        input.value = val;
    }
};

window.validarHora = function(input) {
    let val = input.value.replace(/\D/g, '');
    if (!val) { input.value = ''; return; }

    let h = 0, m = 0;

    if (val.length === 1 || val.length === 2) {
        h = parseInt(val);
        m = 0;
    } else if (val.length === 3) {
        h = parseInt(val.substring(0, 1));
        m = parseInt(val.substring(1, 3));
    } else if (val.length === 4) {
        h = parseInt(val.substring(0, 2));
        m = parseInt(val.substring(2, 4));
    }

    if (h > 12) h = 12;
    if (h === 0) h = 12;
    if (m > 59) m = 59;

    input.value = `${h}:${m.toString().padStart(2, '0')}`;
};

window.setAMPM = function(tipo, valor, sincInicio = true) {
    document.getElementById(`hora_ampm_${tipo}`).value = valor;
    const btnAM = document.getElementById(`btn_am_${tipo}`);
    const btnPM = document.getElementById(`btn_pm_${tipo}`);

    if(valor === 'AM') {
        btnAM.className = "px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnPM.className = "px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    } else {
        btnPM.className = "px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnAM.className = "px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    }

    if (tipo === 'inicio' && sincInicio) {
        window.setAMPM('fin', valor, false);
    }
};

async function cargarCitasDesdeBD() {
    try {
        const datos = await window.API.get('/api/citas');
        citasDB = datos;
        actualizarEstadisticas();
        inicializarPaginador();
        filtrarDatos(); 
    } catch (error) {
        window.Alerta.error("Error de conexión", "No se pudieron cargar las citas.");
    }
}

async function cargarCitasSilencioso() {
    try {
        const datos = await window.API.get('/api/citas');
        citasDB = datos;
        actualizarEstadisticas();
        filtrarDatos();
    } catch (error) {
        // Modo silencioso, no asustamos al usuario si el internet falla 5 segundos
    }
}

async function cargarDatosFormulario() {
    const selectDoctor = document.querySelector('select[name="empleado_id"]');
    try {
        const respuesta = await fetch('/api/citas/datos-formulario');
        if (!respuesta.ok) throw new Error("Error del servidor: " + respuesta.status);

        const datos = await respuesta.json();
        if(datos.pacientes) pacientesGlobal = datos.pacientes;

        if(selectDoctor) {
            selectDoctor.innerHTML = '<option value="">Seleccione un doctor...</option>';
            datos.doctores.forEach(d => {
                selectDoctor.innerHTML += `<option value="${d.id}">${d.nombre_completo}</option>`;
            });
        }
    } catch (error) {
        window.Alerta.error("Error de carga", "Hubo un problema al cargar los datos del formulario.");
    }
}

function configurarBuscadorPacientes() {
    const inputBuscador = document.getElementById('buscador_paciente');
    const dropdown = document.getElementById('dropdown_pacientes');
    const inputOcultoId = document.getElementById('paciente_id');

    if(!inputBuscador) return;

    const renderizarLista = (termino = "") => {
        dropdown.innerHTML = '';
        if (termino.trim().length === 0) inputOcultoId.value = '';

        const palabrasBusqueda = termino.toLowerCase().split(' ').filter(p => p.trim() !== '');

        const filtrados = pacientesGlobal.filter(p => {
            const nombreCompleto = p.nombre_completo.toLowerCase();
            return palabrasBusqueda.every(palabra => nombreCompleto.includes(palabra));
        });

        if (filtrados.length === 0) {
            dropdown.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500 italic">No se encontraron pacientes</div>`;
        } else {
            filtrados.forEach(p => {
                const item = document.createElement('div');
                item.className = "px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm text-slate-700 font-medium transition-colors border-b border-slate-50 last:border-0";
                item.innerHTML = p.nombre_completo;
                
                item.onclick = () => {
                    inputBuscador.value = p.nombre_completo;
                    inputOcultoId.value = p.id;
                    dropdown.classList.add('hidden');
                };
                dropdown.appendChild(item);
            });
        }
        dropdown.classList.remove('hidden');
    };

    inputBuscador.addEventListener('input', function() { renderizarLista(this.value); });
    inputBuscador.addEventListener('focus', function() { renderizarLista(this.value); });
    document.addEventListener('click', (e) => {
        if (!inputBuscador.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden');
    });
}

function format24h(horaString, ampm) {
    let [hours, minutes] = horaString.split(':');
    let hours24 = parseInt(hours, 10);
    if (ampm === 'PM' && hours24 < 12) hours24 += 12;
    if (ampm === 'AM' && hours24 === 12) hours24 = 0;
    return `${hours24.toString().padStart(2, '0')}:${minutes}:00`;
}

window.guardarDatos = async function() {
    const form = document.getElementById('formCita');
    const pacienteSeleccionado = document.getElementById('paciente_id').value;
    
    const horaVisualInicio = document.getElementById('hora_input_inicio').value;
    const ampmInicio = document.getElementById('hora_ampm_inicio').value;
    
    const horaVisualFin = document.getElementById('hora_input_fin').value;
    const ampmFin = document.getElementById('hora_ampm_fin').value;
    
    if (!form.checkValidity() || pacienteSeleccionado === '' || horaVisualInicio.length < 4 || horaVisualFin.length < 4) {
        form.reportValidity(); 
        if(window.Alerta) window.Alerta.advertencia('Campos incompletos', 'Completa todos los campos y asegúrate de llenar correctamente Hora de Inicio y Fin.');
        return;
    }

    const horaFinalInicio = format24h(horaVisualInicio, ampmInicio);
    const horaFinalFin = format24h(horaVisualFin, ampmFin);
    
    document.getElementById('hora_oculta_inicio').value = horaFinalInicio;
    document.getElementById('hora_oculta_fin').value = horaFinalFin;

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const intentarGuardar = async (payload) => {
        try {
            let response;
            if (payload.id && payload.id !== '') {
                response = await window.API.put('/api/citas/' + payload.id, payload);
            } else {
                response = await window.API.post('/api/citas', payload);
            }
            
            if (response && response.success) {
                window.closeModal('modalCitas');
                if(window.Alerta) window.Alerta.exito('¡Éxito!', response.mensaje || 'La cita fue guardada correctamente.');
                cargarCitasDesdeBD(); 
            }

        } catch (error) {
            if (error.status === 409 && error.data && error.data.warning) {
                if (typeof Swal !== 'undefined') {
                    const result = await Swal.fire({
                        title: 'Horario Ocupado',
                        text: error.data.mensaje,
                        icon: 'warning',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Agendar', 
                        denyButtonText: 'Cambiar hora',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        focusCancel: true,
                        scrollbarPadding: false,
                        heightAuto: false,
                        customClass: { 
                            popup: 'da-modal da-danger', 
                            confirmButton: 'da-btn da-btn-danger', 
                            denyButton: 'da-btn da-btn-confirm', 
                            cancelButton: 'da-btn da-btn-cancel' 
                        },
                        buttonsStyling: false
                    });

                    if (result.isConfirmed) {
                        payload.forzar_guardado = true;
                        await intentarGuardar(payload);
                    } 
                }
            } else if (error.status === 422 && error.data && error.data.error) {
                if(window.Alerta) window.Alerta.error('Error', error.data.error);
            } else {
                if(window.Alerta) window.Alerta.error('Error del servidor', 'Hubo un problema al guardar la cita.');
            }
        }
    };

    await intentarGuardar(data);
};

function actualizarEstadisticas() {
    const hoy = new Date().toISOString().split('T')[0];
    const countHoy = citasDB.filter(c => c.fecha === hoy && c.estado !== 'Cancelada').length;
    const countPendientes = citasDB.filter(c => c.estado === 'Programada' || c.estado === 'Confirmada').length;
    
    const countSemana = citasDB.filter(c => {
        if(c.estado === 'Cancelada') return false;
        const fechaCita = new Date(c.fecha);
        const fechaHoy = new Date();
        const diffTime = Math.abs(fechaCita - fechaHoy);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
        return diffDays <= 7;
    }).length;

    const statHoy = document.getElementById('statHoy');
    const statSemana = document.getElementById('statSemana');
    const statPend = document.getElementById('statPendientes');

    if(statHoy) statHoy.innerText = countHoy;
    if(statSemana) statSemana.innerText = countSemana;
    if(statPend) statPend.innerText = countPendientes;
}

function parseTo12h(timeString) {
    if(!timeString) return '';
    let [hours, minutes] = timeString.split(':');
    let h12 = parseInt(hours, 10);
    const ampm = h12 >= 12 ? 'PM' : 'AM';
    h12 = h12 % 12;
    h12 = h12 ? h12 : 12; 
    return `${h12}:${minutes} ${ampm}`; 
}

function inicializarPaginador() {
    miPaginadorCitas = new PaginadorTabla(citasDB, 6, {
        tableBodyId: 'citasTableBody',
        
        renderRow: (c) => {
            let estadoClass = "";
            switch(c.estado) {
                case 'Confirmada': estadoClass = "bg-blue-100 text-blue-700 border-blue-200"; break;
                case 'Programada': estadoClass = "bg-amber-100 text-amber-700 border-amber-200"; break;
                case 'Cancelada': estadoClass = "bg-rose-100 text-rose-700 border-rose-200"; break;
                case 'Completada': estadoClass = "bg-emerald-100 text-emerald-700 border-emerald-200"; break;
                case 'En progreso': estadoClass = "bg-purple-100 text-purple-700 border-purple-200 animate-pulse"; break;
                default: estadoClass = "bg-slate-100 text-slate-500 border-slate-200";
            }

            const fechaObj = new Date(c.fecha + 'T' + (c.hora || '00:00:00'));
            const fechaLegible = fechaObj.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            
            let rangoHora = `${parseTo12h(c.hora)} - ${parseTo12h(c.hora_fin)}`;

            let filaEstilo = c.estado === 'Cancelada' ? 'opacity-50 bg-slate-50' : 'hover:bg-slate-50';

            return `
                <tr class="${filaEstilo} border-b border-slate-100 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${fechaLegible}</span>
                            <span class="text-[11px] text-slate-400 font-bold tracking-tight">${rangoHora}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-blue-800 text-sm block">${c.paciente}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">${c.motivo || 'Sin motivo'}</td>
                    <td class="px-6 py-4 text-sm text-slate-500 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                            ${c.doctor || 'No asignado'}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="${estadoClass} border text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide inline-block min-w-[90px] text-center shadow-sm">
                            ${c.estado}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="window.abrirModalEdicion(${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm border border-emerald-100" title="Editar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            </button>
                            <button onclick="window.eliminarCita(${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200" title="Cancelar Cita">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        },
        updateInfo: (start, end, total) => {
            const info = document.getElementById('paginationInfo');
            if(info) info.innerHTML = `Mostrando <span class="font-bold text-slate-900">${start}-${end}</span> de <span class="font-bold text-slate-900">${total}</span> citas`;
        }
    });
}

function configurarFiltros() {
    const input = document.getElementById('searchInput');
    if(input) input.addEventListener('input', filtrarDatos);
    
    const select = document.getElementById('filtroEstado');
    if(select) {
        let optionActivas = Array.from(select.options).find(opt => opt.value === 'activas');
        if (!optionActivas) {
            optionActivas = document.createElement('option');
            optionActivas.value = 'activas';
            optionActivas.text = 'Pendientes y En Curso';
            select.insertBefore(optionActivas, select.options[0]);
            
            if(select.value === "" || select.value === "Todos los estados") {
                select.value = 'activas';
            }
        }
        select.addEventListener('change', filtrarDatos);
    }
}

function filtrarDatos() {
    const input = document.getElementById('searchInput');
    const term = input ? input.value.toLowerCase() : '';
    
    const select = document.getElementById('filtroEstado');
    const estado = select ? select.value : '';

    const filtrados = citasDB.filter(c => {
        const motivo = c.motivo || '';
        const paciente = c.paciente || '';
        const doctor = c.doctor || '';
        const matchText = paciente.toLowerCase().includes(term) || motivo.toLowerCase().includes(term) || doctor.toLowerCase().includes(term);
        
        let matchEstado = true;
        if (estado === 'activas') {
            matchEstado = ['Programada', 'Confirmada', 'En progreso'].includes(c.estado);
        } else if (estado !== "") {
            matchEstado = c.estado === estado;
        }

        return matchText && matchEstado;
    });

    if (miPaginadorCitas) miPaginadorCitas.setData(filtrados);
}

window.openModal = async function(modalID, mode = 'add') {
    const modal = document.getElementById(modalID);
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('formCita');
    
    await cargarDatosFormulario();

    if(mode === 'add') {
        title.innerText = 'Nueva Cita';
        form.reset();
        form.id.value = '';
        form.fecha.value = new Date().toISOString().split('T')[0];
        document.getElementById('buscador_paciente').value = ''; 
        document.getElementById('paciente_id').value = ''; 

        document.getElementById('hora_input_inicio').value = '';
        window.setAMPM('inicio', 'AM'); 
        
        document.getElementById('hora_input_fin').value = '';

    } else {
        title.innerText = 'Editar Cita';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.modal-backdrop').classList.remove('opacity-0');
        modal.querySelector('.modal-panel').classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
        modal.querySelector('.modal-panel').classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    }, 10);
};

window.closeModal = function(modalID) {
    const modal = document.getElementById(modalID);
    modal.querySelector('.modal-backdrop').classList.add('opacity-0');
    const panel = modal.querySelector('.modal-panel');
    panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');
    panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

window.abrirModalEdicion = async function(id) {
    const c = citasDB.find(x => x.id == id);
    if(!c) return;

    await window.openModal('modalCitas', 'edit');

    const form = document.getElementById('formCita');
    form.id.value = c.id;
    form.empleado_id.value = c.empleado_id;
    form.fecha.value = c.fecha;
    form.motivo.value = c.motivo;
    form.estado.value = c.estado;
    form.notas.value = c.notas || '';
    form.paciente_id.value = c.paciente_id;
    document.getElementById('buscador_paciente').value = c.paciente || ''; 

    if (c.hora) {
        let [h, m] = c.hora.split(':');
        let h12 = parseInt(h, 10);
        const ampm = h12 >= 12 ? 'PM' : 'AM';
        h12 = h12 % 12; h12 = h12 ? h12 : 12;
        document.getElementById('hora_input_inicio').value = `${h12}:${m}`;
        window.setAMPM('inicio', ampm, false); 
    }
    
    if (c.hora_fin) {
        let [h, m] = c.hora_fin.split(':');
        let h12 = parseInt(h, 10);
        const ampm = h12 >= 12 ? 'PM' : 'AM';
        h12 = h12 % 12; h12 = h12 ? h12 : 12;
        document.getElementById('hora_input_fin').value = `${h12}:${m}`;
        window.setAMPM('fin', ampm, false);
    }
};

window.eliminarCita = async function(id) {
    if(!window.Alerta) return; 
    const confirmado = await window.Alerta.eliminar('¿Cancelar Cita?', 'Esta cita se marcará como "Cancelada".');
    if(confirmado) {
        try {
            const response = await window.API.delete('/api/citas/' + id);
            if(response && response.success) {
                window.Alerta.exito('Cancelada', 'La cita fue enviada al fondo de la tabla.');
                cargarCitasDesdeBD(); 
            }
        } catch (error) {
            window.Alerta.error('Hubo un problema', 'No se pudo cancelar la cita.');
        }
    }
};