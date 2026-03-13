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

        // Guardamos los pacientes en la variable global para el buscador
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

// 3. Lógica del buscador de pacientes en el modal
function configurarBuscadorPacientes() {
    const inputBuscador = document.getElementById('buscador_paciente');
    const dropdown = document.getElementById('dropdown_pacientes');
    const inputOcultoId = document.getElementById('paciente_id');

    if(!inputBuscador) return;

    inputBuscador.addEventListener('input', function() {
        const termino = this.value.toLowerCase();
        dropdown.innerHTML = '';

        if (termino.length < 1) {
            dropdown.classList.add('hidden');
            inputOcultoId.value = ''; 
            return;
        }

        const filtrados = pacientesGlobal.filter(p => p.nombre_completo.toLowerCase().includes(termino));

        if (filtrados.length === 0) {
            dropdown.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500 italic">No se encontraron pacientes</div>`;
        } else {
            filtrados.forEach(p => {
                const item = document.createElement('div');
                item.className = "px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm text-slate-700 font-medium transition-colors border-b border-slate-50 last:border-0";
                item.textContent = p.nombre_completo;
                
                item.onclick = () => {
                    inputBuscador.value = p.nombre_completo;
                    inputOcultoId.value = p.id;
                    dropdown.classList.add('hidden');
                };
                dropdown.appendChild(item);
            });
        }
        dropdown.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!inputBuscador.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

// 4. Procesar el botón Guardar usando las alertas
window.guardarDatos = async function() {
    const form = document.getElementById('formCita');
    const pacienteSeleccionado = document.getElementById('paciente_id').value;
    
    if (!form.checkValidity() || pacienteSeleccionado === '') {
        form.reportValidity(); 
        Alerta.advertencia('Campos incompletos', 'Por favor completa los campos obligatorios (*) y selecciona un paciente de la lista.');
        return;
    }

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
        Alerta.error('Error del servidor', 'Hubo un problema al guardar la cita.');
    }
};

// 5. Actualizar Estadísticas
function actualizarEstadisticas() {
    const hoy = new Date().toISOString().split('T')[0];
    
    const countHoy = citasDB.filter(c => c.fecha === hoy).length;
    const countPendientes = citasDB.filter(c => c.estado === 'Programada' || c.estado === 'Confirmada').length;
    
    const countSemana = citasDB.filter(c => {
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

            const fechaObj = new Date(c.fecha + 'T' + c.hora);
            const fechaLegible = fechaObj.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            const horaMin = c.hora ? c.hora.substring(0, 5) : '';

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${fechaLegible}</span>
                            <span class="text-xs text-slate-400 font-medium">${horaMin} hrs</span>
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
                            <button onclick="window.eliminarCita(${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200" title="Eliminar/Cancelar">
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
    form.hora.value = c.hora;
    form.motivo.value = c.motivo;
    form.estado.value = c.estado;
    form.notas.value = c.notas || '';
    
    form.paciente_id.value = c.paciente_id;
    document.getElementById('buscador_paciente').value = c.paciente || ''; 
};

// 9. Botón Basura con Alertas personalizadas
window.eliminarCita = async function(id) {
    const confirmado = await Alerta.eliminar(
        '¿Cancelar Cita?', 
        'Esta cita desaparecerá de la lista activa.'
    );

    if(confirmado) {
        try {
            const response = await window.API.delete('/api/citas/' + id);
            if(response && response.success) {
                Alerta.exito('¡Cancelada!', 'La cita ha sido ocultada exitosamente.');
                cargarCitasDesdeBD(); 
            }
        } catch (error) {
            console.error("Error al eliminar:", error);
            Alerta.error('Hubo un problema', 'No se pudo cancelar la cita.');
        }
    }
};