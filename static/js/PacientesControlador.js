// static/js/pacientesControlador.js

// --- 1. BASE DE DATOS SIMULADA (COMPLETA) ---
// Incluye historial de citas y tratamientos para que el PDF salga completo como querías.
let pacientesDB = [
    { 
        id: 1, 
        expediente: '2024-001', 
        nombre: 'María', 
        apellido: 'González', 
        telefono: '7777-8888', 
        email: 'maria.g@email.com', 
        fecha_nacimiento: '1990-05-15', 
        genero: 'Femenino', 
        direccion: 'Col. San Benito', 
        ciudad: 'San Salvador', 
        codigo_postal: '01101', 
        // Emergencia
        contacto_nombre: 'Pedro González', 
        contacto_tel: '6666-5555', 
        // Médico
        seguro: 'Seguros del País', 
        alergias: 'Ninguna', 
        cronicas: 'Ninguna', 
        medicamentos: 'Ninguno', 
        notas: '', 
        activo: 1,
        // Historial para PDF
        citas: [
            { fecha: '15/01/2024', motivo: 'Revisión General', estado: 'Completada' },
            { fecha: '20/03/2024', motivo: 'Limpieza Dental', estado: 'Completada' }
        ],
        tratamientos: [
            { nombre: 'Limpieza Dental', fecha: '15/01/2024', costo: '$35.00' },
            { nombre: 'Consulta General', fecha: '20/03/2024', costo: '$25.00' }
        ]
    },
    { 
        id: 2, 
        expediente: '2024-002', 
        nombre: 'Carlos', 
        apellido: 'Martínez', 
        telefono: '6666-7777', 
        email: 'carlos.m@email.com', 
        fecha_nacimiento: '1985-08-22', 
        genero: 'Masculino', 
        direccion: 'Col. Escalón', 
        ciudad: 'San Salvador', 
        codigo_postal: '01102', 
        contacto_nombre: 'Ana Martínez', 
        contacto_tel: '7777-9999', 
        seguro: 'Aseguradora Suiza', 
        alergias: 'Penicilina', 
        cronicas: 'Hipertensión', 
        medicamentos: 'Losartán 50mg', 
        notas: 'Paciente nervioso', 
        activo: 0,
        citas: [
            { fecha: '10/02/2024', motivo: 'Dolor de muela', estado: 'Completada' }
        ],
        tratamientos: [
            { nombre: 'Extracción Dental', fecha: '10/02/2024', costo: '$75.00' }
        ]
    },
    { 
        id: 3, 
        expediente: '2024-003', 
        nombre: 'Ana', 
        apellido: 'Rodríguez', 
        telefono: '7888-9999', 
        email: 'ana.r@email.com', 
        fecha_nacimiento: '1995-03-10', 
        genero: 'Femenino', 
        direccion: 'Col. Miramonte', 
        ciudad: 'San Salvador', 
        codigo_postal: '01103', 
        contacto_nombre: 'Luis Rodríguez', 
        contacto_tel: '7888-0000', 
        seguro: 'Plan Básico', 
        alergias: 'Látex', 
        cronicas: 'Asma', 
        medicamentos: 'Salbutamol', 
        notas: 'Usar guantes de nitrilo', 
        activo: 1,
        citas: [], 
        tratamientos: [] 
    },
    { id: 4, expediente: '2024-004', nombre: 'Jose', apellido: 'Hernández', telefono: '7555-4444', fecha_nacimiento: '1988-11-30', genero: 'Masculino', activo: 1, citas:[], tratamientos:[] },
    { id: 5, expediente: '2024-005', nombre: 'Sofía', apellido: 'López', telefono: '6777-8888', fecha_nacimiento: '1992-07-18', genero: 'Femenino', activo: 1, citas:[], tratamientos:[] },
    { id: 6, expediente: '2024-006', nombre: 'Pedro', apellido: 'Ramírez', telefono: '7111-2222', fecha_nacimiento: '1990-01-01', genero: 'Masculino', activo: 0, citas:[], tratamientos:[] }
];

let miPaginador; 

// --- 2. INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', () => {
    // Verificamos que las utilidades estén cargadas
    if (typeof PaginadorTabla === 'undefined') {
        console.error("Error Crítico: No se cargó paginadorTabla.js. Verifica el orden de los scripts en el PHP.");
        return;
    }

    inicializarPaginador();
    configurarBusqueda();
    
    // Configuración de Tabs del Modal (Estética)
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', () => {
            // Desactivar todos
            document.querySelectorAll('.tab-btn').forEach(t => {
                t.classList.remove('border-blue-800', 'text-blue-800');
                t.classList.add('border-transparent', 'text-slate-500');
            });
            // Activar actual
            tab.classList.remove('border-transparent', 'text-slate-500');
            tab.classList.add('border-blue-800', 'text-blue-800');
            
            // Mostrar contenido
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(tab.dataset.target).classList.remove('hidden');
        });
    });
});

function inicializarPaginador() {
    miPaginador = new PaginadorTabla(pacientesDB, 5, {
        tableBodyId: 'patientsTableBody',
        
        // --- RENDERIZADO DE FILA (DISEÑO) ---
        renderRow: (p) => {
            const edad = p.fecha_nacimiento ? new Date().getFullYear() - new Date(p.fecha_nacimiento).getFullYear() : '-';
            
            // Badges de Estado
            const estadoClass = p.activo 
                ? "bg-emerald-100 text-emerald-700 border border-emerald-200" 
                : "bg-slate-100 text-slate-500 border border-slate-200";
            const estadoTexto = p.activo ? "Activo" : "Inactivo";

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors">
                    <td class="px-6 py-4 font-bold text-blue-800 text-sm">${p.expediente}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${p.nombre} ${p.apellido}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">${p.genero || ''}</span>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">${p.telefono || '-'}</td>
                    
                    <td class="px-6 py-4 text-sm text-slate-500">${edad} años</td>
                    
                    <td class="px-6 py-4">
                        <span class="${estadoClass} text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">${estadoTexto}</span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="window.imprimirExpediente(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100" title="Generar PDF">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                                </svg>
                            </button>
                            
                            <button onclick="window.abrirModalEdicion(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm border border-emerald-100" title="Editar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                            
                            <button onclick="window.eliminarPaciente(${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-slate-200" title="Eliminar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        },
        
        // --- ACTUALIZACIÓN DE TEXTO DE PAGINACIÓN ---
        updateInfo: (start, end, total) => {
            const info = document.getElementById('paginationInfo');
            if(info) info.innerHTML = `Mostrando <span class="font-bold text-slate-900">${start}-${end}</span> de <span class="font-bold text-slate-900">${total}</span> pacientes`;
        }
    });
}

function configurarBusqueda() {
    const input = document.getElementById('searchInput');
    if(!input) return;
    
    input.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtrados = pacientesDB.filter(p => 
            (p.nombre + ' ' + p.apellido).toLowerCase().includes(term) || 
            p.expediente.toLowerCase().includes(term)
        );
        miPaginador.setData(filtrados);
    });
}

// --- 3. FUNCIONES GLOBALES (ACCIONES) ---

// ABRIR MODAL
window.openModal = function(modalID, mode = 'add') {
    const modal = document.getElementById(modalID);
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('formPaciente');
    
    // Resetear a pestaña 1
    document.querySelector('.tab-btn[data-target="tab-personal"]').click();

    if(mode === 'add') {
        title.innerText = 'Nuevo Paciente';
        form.reset();
        form.id.value = '';
        // Generar Expediente Automático
        const nextId = String(pacientesDB.length + 1).padStart(3, '0');
        form.expediente.value = `2024-${nextId}`;
        form.ciudad.value = 'San Salvador';
    } else {
        title.innerText = 'Editar Paciente';
    }

    modal.classList.remove('hidden');
    // Animación de entrada
    setTimeout(() => {
        modal.querySelector('.modal-backdrop').classList.remove('opacity-0');
        modal.querySelector('.modal-panel').classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
        modal.querySelector('.modal-panel').classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    }, 10);
};

// CERRAR MODAL
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

// EDITAR (LLENADO DE DATOS COMPLETO)
window.abrirModalEdicion = function(id) {
    const p = pacientesDB.find(x => x.id === id);
    if(!p) return;

    const form = document.getElementById('formPaciente');
    
    // Datos Personales
    form.id.value = p.id;
    form.expediente.value = p.expediente;
    form.nombre.value = p.nombre;
    form.apellido.value = p.apellido;
    form.fecha_nacimiento.value = p.fecha_nacimiento || '';
    form.genero.value = p.genero;
    form.direccion.value = p.direccion || '';
    form.ciudad.value = p.ciudad || '';
    form.codigo_postal.value = p.codigo_postal || '';
    form.activo.value = p.activo;
    
    // Contacto
    form.telefono.value = p.telefono || '';
    form.email.value = p.email || '';
    form.contacto_emergencia_nombre.value = p.contacto_nombre || '';
    form.contacto_emergencia_telefono.value = p.contacto_tel || '';
    
    // Médico (Estos campos faltaban antes, ahora están)
    form.seguro_medico.value = p.seguro || '';
    form.alergias.value = p.alergias || '';
    form.enfermedades_cronicas.value = p.cronicas || '';
    form.medicamentos_actuales.value = p.medicamentos || '';
    form.notas_medicas.value = p.notas || '';

    window.openModal('modalPacientes', 'edit');
};

// ELIMINAR
window.eliminarPaciente = function(id) {
    if(confirm('¿Está seguro de eliminar este expediente permanentemente?')) {
        pacientesDB = pacientesDB.filter(p => p.id !== id);
        miPaginador.setData(pacientesDB);
    }
};

// IMPRIMIR PDF (CON TODOS LOS DATOS EXTRA)
window.imprimirExpediente = function(id) {
    const p = pacientesDB.find(x => x.id === id);
    if(!p) return;
    
    const edad = p.fecha_nacimiento ? new Date().getFullYear() - new Date(p.fecha_nacimiento).getFullYear() : 'N/A';
    
    // Preparar objeto para el generador
    const configReporte = {
        folio: p.expediente,
        nombreArchivo: `Expediente_${p.expediente}_${p.apellido}`,
        // Datos Planos para las secciones
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
            notas: p.notas
        },
        // Arrays para las tablas del PDF
        citas: p.citas || [],
        tratamientos: p.tratamientos || []
    };
    
    // Llamar al módulo de reportes
    if(typeof ReportePDF !== 'undefined') {
        ReportePDF.generar(configReporte);
    } else {
        alert("Error: El módulo ReportePDF no está cargado.");
    }
};