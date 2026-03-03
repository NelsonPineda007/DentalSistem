// static/js/citasControlador.js

// --- 1. BASE DE DATOS SIMULADA ---
let citasDB = [
    { id: 1, fecha: '2024-02-16', hora: '09:00', paciente: 'María González', motivo: 'Limpieza Dental', doctor: 'Dr. General', estado: 'Confirmada', notas: '' },
    { id: 2, fecha: '2024-02-16', hora: '10:30', paciente: 'Carlos Martínez', motivo: 'Dolor Agudo', doctor: 'Dr. General', estado: 'Pendiente', notas: '' },
    { id: 3, fecha: '2024-02-17', hora: '15:00', paciente: 'Ana Rodríguez', motivo: 'Revisión Ortodoncia', doctor: 'Dra. Ortodoncista', estado: 'Confirmada', notas: '' },
    { id: 4, fecha: '2024-02-18', hora: '11:00', paciente: 'Jose Hernández', motivo: 'Extracción', doctor: 'Dr. Cirujano', estado: 'Cancelada', notas: 'Reprogramada' },
    { id: 5, fecha: '2024-02-16', hora: '16:00', paciente: 'Sofía López', motivo: 'Blanqueamiento', doctor: 'Dra. Estética', estado: 'Pendiente', notas: '' },
    { id: 6, fecha: '2024-02-10', hora: '09:00', paciente: 'Pedro Ramírez', motivo: 'Consulta General', doctor: 'Dr. General', estado: 'Completada', notas: '' },
    { id: 7, fecha: '2024-02-20', hora: '14:00', paciente: 'Lucía Méndez', motivo: 'Carilla Dental', doctor: 'Dra. Estética', estado: 'Pendiente', notas: '' },
    { id: 8, fecha: '2024-02-16', hora: '17:30', paciente: 'Mario Ruiz', motivo: 'Urgencia', doctor: 'Dr. General', estado: 'Pendiente', notas: '' },
];

let miPaginadorCitas; 

// --- 2. INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', () => {
    if (typeof PaginadorTabla === 'undefined') {
        console.error("Error: paginadorTabla.js no cargado.");
        return;
    }

    actualizarEstadisticas();
    inicializarPaginador();
    configurarFiltros();
});

function actualizarEstadisticas() {
    const hoy = new Date().toISOString().split('T')[0];
    const countHoy = citasDB.filter(c => c.fecha === hoy && c.estado !== 'Cancelada').length;
    const countPendientes = citasDB.filter(c => c.estado === 'Pendiente').length;
    
    // Semana simple
    const countSemana = citasDB.filter(c => {
        const fechaCita = new Date(c.fecha);
        const fechaHoy = new Date();
        const diffTime = Math.abs(fechaCita - fechaHoy);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
        return diffDays <= 7 && c.estado !== 'Cancelada';
    }).length;

    document.getElementById('statHoy').innerText = countHoy;
    document.getElementById('statSemana').innerText = countSemana;
    document.getElementById('statPendientes').innerText = countPendientes;
}

function inicializarPaginador() {
    miPaginadorCitas = new PaginadorTabla(citasDB, 6, {
        tableBodyId: 'citasTableBody',
        
        renderRow: (c) => {
            let estadoClass = "";
            switch(c.estado) {
                case 'Confirmada': estadoClass = "bg-blue-100 text-blue-700 border-blue-200"; break;
                case 'Pendiente': estadoClass = "bg-amber-100 text-amber-700 border-amber-200"; break;
                case 'Cancelada': estadoClass = "bg-rose-100 text-rose-700 border-rose-200"; break;
                case 'Completada': estadoClass = "bg-emerald-100 text-emerald-700 border-emerald-200"; break;
                default: estadoClass = "bg-slate-100 text-slate-500 border-slate-200";
            }

            const fechaObj = new Date(c.fecha + 'T' + c.hora);
            const fechaLegible = fechaObj.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });

            // AQUI ESTAN LOS CAMBIOS DE LOS BOTONES:
            // Se usan exactamente las clases de pacientes.php
            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${fechaLegible}</span>
                            <span class="text-xs text-slate-400 font-medium">${c.hora} hrs</span>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <span class="font-bold text-blue-800 text-sm block">${c.paciente}</span>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">${c.motivo}</td>
                    
                    <td class="px-6 py-4 text-sm text-slate-500 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                            ${c.doctor}
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
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                            
                            <button onclick="window.eliminarCita(${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200" title="Eliminar/Cancelar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
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
        const matchText = c.paciente.toLowerCase().includes(term) || c.motivo.toLowerCase().includes(term);
        const matchEstado = estado === "" || c.estado === estado;
        return matchText && matchEstado;
    });

    miPaginadorCitas.setData(filtrados);
}

// --- 3. ACCIONES DEL SISTEMA ---

window.openModal = function(modalID, mode = 'add') {
    const modal = document.getElementById(modalID);
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('formCita');
    
    if(mode === 'add') {
        title.innerText = 'Nueva Cita';
        form.reset();
        form.id.value = '';
        form.fecha.value = new Date().toISOString().split('T')[0];
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

window.abrirModalEdicion = function(id) {
    const c = citasDB.find(x => x.id === id);
    if(!c) return;

    const form = document.getElementById('formCita');
    
    form.id.value = c.id;
    form.fecha.value = c.fecha;
    form.hora.value = c.hora;
    form.motivo.value = c.motivo;
    form.doctor.value = c.doctor;
    form.estado.value = c.estado;
    form.notas.value = c.notas || '';

    window.openModal('modalCitas', 'edit');
};

window.eliminarCita = function(id) {
    if(confirm('¿Desea cancelar/eliminar esta cita?')) {
        citasDB = citasDB.filter(c => c.id !== id);
        miPaginadorCitas.setData(citasDB);
        actualizarEstadisticas();
    }
};