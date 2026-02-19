<?php // navbar ?>
<aside class="w-72 bg-white h-full flex flex-col justify-between shadow-xl shadow-blue-900/10 border-r border-gray-100 flex-shrink-0">

<?php // logo y titulo de la pagina ?>
    <div class="p-8 flex items-center gap-1">
        <img 
            src="../static/imgs/logo-diente.png" 
            alt="Logo Dentista" 
            class="w-20 h-auto object-contain">

        <h1 class="text-2xl font-bold text-blue-800 tracking-wide">DENTISTA</h1>
    </div>

<?php // Comienzo del navbar ?>
    <nav class="flex-1 px-6 space-y-2 overflow-y-auto">
        <ul class="space-y-4">

            <li>
                <a href="dashboard.php"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="citas.php"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Citas
                </a>
            </li>

            <li>
                <a href="pacientes.php"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                    Pacientes
                </a>
            </li>

            <li>
                <a href="tratamiento.php"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Tratamientos
                </a>
            </li>

        </ul>
    </nav>

<?php // Usuario y cerrar sesion ?>
    <div class="p-6 border-t border-gray-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-blue-800 shadow-lg shadow-blue-900/30 flex items-center justify-center text-blue-100">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                          clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-blue-800">Usuario</p>
                <p class="text-xs text-blue-400">Administrador</p>
            </div>
        </div>

        <button
            class="w-full flex items-center justify-center gap-2 bg-blue-800 hover:bg-blue-900 text-blue-100 py-2.5 px-4 rounded-xl shadow-xl shadow-blue-900/30 transition font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                </path>
            </svg>
            Salir
        </button>
    </div>

</aside>



