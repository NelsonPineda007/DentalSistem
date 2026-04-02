document.addEventListener('DOMContentLoaded', () => {
    cargarDatosPerfil();

    // Evento Formulario Datos
    const formPerfil = document.getElementById('formPerfil');
    if(formPerfil) {
        formPerfil.addEventListener('submit', async (e) => {
            e.preventDefault();
            actualizarPerfil();
        });
    }

    // NUEVO EVENTO: Botón de solicitar correo
    const btnSolicitarPass = document.getElementById('btn-solicitar-password');
    if (btnSolicitarPass) {
        btnSolicitarPass.addEventListener('click', solicitarCorreoPassword);
    }
});

// Función para cambiar de Pestañas
window.switchTab = function(tabId, botonSeleccionado) {
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.add('hidden');
        c.classList.remove('block');
    });
    
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('block');

    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-800', 'text-blue-800');
        b.classList.add('border-transparent', 'text-slate-500');
    });

    botonSeleccionado.classList.remove('border-transparent', 'text-slate-500');
    botonSeleccionado.classList.add('border-blue-800', 'text-blue-800');
}

// 1. CARGAR DATOS DEL USUARIO (Usando fetch puro y seguro)
async function cargarDatosPerfil() {
    try {
        const respuesta = await fetch('/api/perfil/datos');
        if (!respuesta.ok) throw new Error('Error al conectar con el servidor');
        
        const data = await respuesta.json();

        // Validamos que el servidor haya respondido status: success
        if (data.status !== 'success') {
            throw new Error(data.message || 'Error en la respuesta del servidor');
        }
        
        // Llenar datos visuales de la tarjeta (con validaciones de seguridad '?')
        const uiNombre = document.getElementById('ui-nombre');
        if(uiNombre) uiNombre.innerText = `Dr(a). ${data.usuario?.nombre || ''} ${data.usuario?.apellido || ''}`;
        
        const uiRol = document.getElementById('ui-rol');
        if(uiRol) uiRol.innerText = data.usuario?.rol || 'Usuario';
        
        const uiEmail = document.getElementById('ui-email');
        if(uiEmail) uiEmail.innerText = data.usuario?.email || 'Sin correo';
        
        const uiTelefono = document.getElementById('ui-telefono');
        if(uiTelefono) uiTelefono.innerText = data.usuario?.telefono || 'Sin teléfono';
        
        const uiCitas = document.getElementById('ui-citas');
        if(uiCitas) uiCitas.innerText = data.estadisticas?.citas_mes || '0';

        const uiEmailSeguridad = document.getElementById('ui-email-seguridad');
        if(uiEmailSeguridad) uiEmailSeguridad.innerText = data.usuario?.email || '';

        // Llenar Formulario de Edición
        const inNombre = document.getElementById('input_nombre');
        if(inNombre) inNombre.value = data.usuario?.nombre || '';
        
        const inApellido = document.getElementById('input_apellido');
        if(inApellido) inApellido.value = data.usuario?.apellido || '';
        
        const inEmail = document.getElementById('input_email');
        if(inEmail) inEmail.value = data.usuario?.email || '';
        
        const inTelefono = document.getElementById('input_telefono');
        if(inTelefono) inTelefono.value = data.usuario?.telefono || '';
        
        const inEspecialidad = document.getElementById('input_especialidad');
        if(inEspecialidad) inEspecialidad.value = data.usuario?.especialidad || '';

        // Renderizar Lista de Actividades
        const listaActividad = document.getElementById('lista-actividad');
        if (listaActividad) {
            if (data.actividad && data.actividad.length > 0) {
                listaActividad.innerHTML = data.actividad.map(log => `
                    <div class="flex gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Acción: <span class="text-blue-600">${log.accion || 'Registro'}</span></p>
                            <p class="text-xs font-medium text-slate-400 mt-1">Módulo: ${log.tabla || 'Sistema'} | ${log.tiempo || ''}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                listaActividad.innerHTML = `<p class="text-slate-400 italic p-4 text-center">No hay actividad reciente para mostrar.</p>`;
            }
        }

    } catch (error) {
        console.error("Detalle del error:", error);
        if(window.Alerta) window.Alerta.error('Error', 'No se pudieron cargar los datos de tu perfil.');
    }
}

// 2. ACTUALIZAR PERFIL (Cambiado a fetch puro)
async function actualizarPerfil() {
    const formData = new FormData(document.getElementById('formPerfil'));
    const payload = Object.fromEntries(formData.entries());

    try {
        const respuesta = await fetch('/api/perfil/actualizar', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await respuesta.json();

        if (respuesta.ok && result.status === 'success') {
            if(window.Alerta) window.Alerta.exito('¡Actualizado!', 'Tu información personal se guardó correctamente.');
            cargarDatosPerfil(); 
        } else {
            throw new Error(result.message || 'No se pudo actualizar el perfil.');
        }
        
    } catch (error) {
        if(window.Alerta) window.Alerta.error('Error al guardar', error.message);
    }
}

// 3. SOLICITAR CORREO DE CONTRASEÑA
async function solicitarCorreoPassword() {
    const btn = document.getElementById('btn-solicitar-password');
    const textoOriginal = btn.innerHTML;
    
    try {
        // Animación de carga
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        btn.disabled = true;

        const respuesta = await fetch('/api/perfil/solicitar-password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        });

        const result = await respuesta.json();

        if(respuesta.ok && result.status === 'success') {
            if(window.Alerta) window.Alerta.exito('Correo Enviado', result.message);
        } else {
            throw new Error(result.message || 'Error al enviar el correo.');
        }

    } catch (error) {
        if(window.Alerta) window.Alerta.error('Ups', error.message);
    } finally {
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }
}