// public/js/CitasControlador.js

let citasDB = []; 
let miPaginadorCitas; 
let pacientesGlobal = []; 

document.addEventListener('DOMContentLoaded', () => {
    if (typeof PaginadorTabla === 'undefined') {
        console.error("Error: paginadorTabla.js no cargado.");
        return;
    }

    cargarCitasDesdeBD();
    configurarFiltros();
    configurarBuscadorPacientes();
});

// ==========================================
// MAGIA DEL INPUT DE HORA INTUITIVO
// ==========================================
window.formatearHora = function(input) {
    // Quita cualquier cosa que no sea número
    let val = input.value.replace(/\D/g, ''); 
    // Auto pone los dos puntos ":"
    if (val.length > 2) {
        val = val.substring(0, 2) + ':' + val.substring(2, 4);
    }
    input.value = val;
};

window.validarHora = function(input) {
    let val = input.value;
    if(val.length === 5) {
        let [h, m] = val.split(':');
        // Si el usuario pone horas locas como 15 o 00, lo reparamos a 12h
        if(parseInt(h) > 12) h = '12';
        if(parseInt(h) === 0) h = '12';
        // Si pone minutos como 89, lo topamos a 59
        if(parseInt(m) > 59) m = '59';
        input.value = `${h}:${m}`;
    }
};

window.setAMPM = function(valor) {
    document.getElementById('hora_ampm').value = valor;
    const btnAM = document.getElementById('btn_am');
    const btnPM = document.getElementById('btn_pm');

    if(valor === 'AM') {
        btnAM.className = "px-3 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnPM.className = "px-3 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    } else {
        btnPM.className = "px-3 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnAM.className = "px-3 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    }
};
// ==========================================

// 1. Cargar las citas a la tabla
async function cargarCitasDesdeBD() {
    try {
        const datos = await window.API.get('/api/citas');
        citasDB = datos;
        
        actualizarEstadisticas();
        inicializarPaginador();
    } catch (error) {
        console.error("Error al cargar las citas:", error);
    }
}

// 2. Llenar los Selects de Doctores y Memoria de Pacientes
async function cargarDatosFormulario() {
    const selectDoctor = document.querySelector('select[name="empleado_id"]');

    try {
        const respuesta = await fetch('/api/citas/datos-formulario');
        if (!respuesta.ok) throw new Error("Error del servidor: " + respuesta.status);

        const datos = await respuesta.json();

        if(datos.pacientes) {
            pacientesGlobal = datos.pacientes;
        }

        if(selectDoctor) {
            selectDoctor.innerHTML = '<option value="">Seleccione un doctor...</option>';
            datos.doctores.forEach(d => {
                selectDoctor.innerHTML += `<option value="${d.id}">${d.nombre_completo}</option>`;
            });
        }
    } catch (error) {
        console.error("Hubo un problema al cargar selects:", error);
    }
}

// 3. Buscador de pacientes
function configurarBuscadorPacientes() {
    const inputBuscador = document.getElementById('buscador_paciente');
    const dropdown = document.getElementById('dropdown_pacientes');
    const inputOcultoId = document.getElementById('paciente_id');

    if(!inputBuscador) return;

    const renderizarLista = (termino = "") => {
        dropdown.innerHTML = '';

        if (termino.trim().length === 0) {
            inputOcultoId.value = '';
        }

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
        if (!inputBuscador.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

// 4. Procesar el botón Guardar
window.guardarDatos = async function() {
    const form = document.getElementById('formCita');
    const pacienteSeleccionado = document.getElementById('paciente_id').value;
    const horaVisual = document.getElementById('hora_input').value;
    const ampm = document.getElementById('hora_ampm').value;
    
    if (!form.checkValidity() || pacienteSeleccionado === '' || horaVisual.length < 5) {
        form.reportValidity(); 
        Alerta.advertencia('Campos incompletos', 'Por favor completa todos los campos y asegúrate que la hora esté completa (Ej: 08:30).');
        return;
    }

    // Traducir formato intuitivo a formato BD (24h)
    let [hours, minutes] = horaVisual.split(':');
    let hours24 = parseInt(hours, 10);
    
    if (ampm === 'PM' && hours24 < 12) hours24 += 12;
    if (ampm === 'AM' && hours24 === 12) hours24 = 0;
    
    document.getElementById('hora_oculta').value = `${hours24.toString().padStart(2, '0')}:${minutes}:00`;

    // Enviar datos
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        let response;
        if (data.id && data.id !== '') {
            response = await window.API.put('/api/citas/' + data.id, data);
        } else {
            response = await window.API.post('/api/citas', data);
        }
        
        if (response && response.success) {
            window.closeModal('modalCitas');
            Alerta.exito('¡Éxito!', response.mensaje || 'La cita fue guardada correctamente.');
            cargarCitasDesdeBD(); 
        }

    } catch (error) {
        console.error("Error al guardar los datos:", error);
        
        if (error.status === 422 && error.data && error.data.error) {
            Alerta.error('Horario Ocupado', error.data.error);
        } else {
            Alerta.error('Error del servidor', 'Hubo un problema al guardar la cita.');
        }
    }
};

// 5. Actualizar Estadísticas
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

    document.getElementById('statHoy').innerText = countHoy;
    document.getElementById('statSemana').innerText = countSemana;
    document.getElementById('statPendientes').innerText = countPendientes;
}

// 6. Armar la tabla 
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
                default: estadoClass = "bg-slate-100 text-slate-500 border-slate-200";
            }

            const fechaObj = new Date(c.fecha + 'T' + (c.hora || '00:00:00'));
            const fechaLegible = fechaObj.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            
            // Convertir hora de BD (24h) a 12h
            let horaFormateada = "";
            if (c.hora) {
                let [horaStr, minStr] = c.hora.split(':');
                let horaNum = parseInt(horaStr, 10);
                let ampm = horaNum >= 12 ? 'PM' : 'AM';
                horaNum = horaNum % 12;
                horaNum = horaNum ? horaNum : 12; 
                horaFormateada = `${horaNum.toString().padStart(2, '0')}:${minStr} ${ampm}`;
            }

            let filaEstilo = c.estado === 'Cancelada' ? 'opacity-50 bg-slate-50' : 'hover:bg-slate-50';

            return `
                <tr class="${filaEstilo} border-b border-slate-100 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${fechaLegible}</span>
                            <span class="text-xs text-slate-400 font-medium">${horaFormateada}</span>
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
    input.addEventListener('input', filtrarDatos);

    const select = document.getElementById('filtroEstado');
    select.addEventListener('change', filtrarDatos);
}

function filtrarDatos() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;

    const filtrados = citasDB.filter(c => {
        const motivo = c.motivo || '';
        const paciente = c.paciente || '';
        const doctor = c.doctor || '';
        
        const matchText = paciente.toLowerCase().includes(term) || 
                          motivo.toLowerCase().includes(term) ||
                          doctor.toLowerCase().includes(term);
                          
        const matchEstado = estado === "" || c.estado === estado;
        return matchText && matchEstado;
    });

    miPaginadorCitas.setData(filtrados);
}

// 7. Funciones del Modal
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

        // Reseteo visual a las 08:00 AM
        document.getElementById('hora_input').value = '08:00';
        window.setAMPM('AM');

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

// 8. Botón Lapicito
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

    // Leer la hora de la BD y separarla para los botones visuales
    if (c.hora) {
        let [hours, minutes] = c.hora.split(':');
        let hours12 = parseInt(hours, 10);
        const ampm = hours12 >= 12 ? 'PM' : 'AM';
        
        hours12 = hours12 % 12;
        hours12 = hours12 ? hours12 : 12;
        
        document.getElementById('hora_input').value = `${hours12.toString().padStart(2, '0')}:${minutes}`;
        window.setAMPM(ampm);
    }
};

// 9. Botón Basura: Cancelar
window.eliminarCita = async function(id) {
    const confirmado = await Alerta.eliminar(
        '¿Cancelar Cita?', 
        'Esta cita se marcará como "Cancelada" y pasará al final de la lista.'
    );

    if(confirmado) {
        try {
            const response = await window.API.delete('/api/citas/' + id);
            if(response && response.success) {
                Alerta.exito('Cancelada', 'La cita fue enviada al fondo de la tabla.');
                cargarCitasDesdeBD(); 
            }
        } catch (error) {
            console.error("Error al cancelar:", error);
            Alerta.error('Hubo un problema', 'No se pudo cancelar la cita.');
        }
    }
};