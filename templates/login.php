<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DENTISTA — Iniciar sesión</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Poppins', 'sans-serif'],
          },
          colors: {
            brand: {
              DEFAULT: '#1e40af', /* Exactamente el blue-800 de tu sidebar */
              dark:    '#1e3a8a', /* blue-900 para el hover */
              light:   '#eff6ff', /* blue-50 para fondos */
            }
          },
          keyframes: {
            fadeUp: {
              '0%':   { opacity: '0', transform: 'translateY(14px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            }
          },
          animation: {
            'fade-up-1': 'fadeUp 0.4s ease 0.05s both',
            'fade-up-2': 'fadeUp 0.4s ease 0.12s both',
            'fade-up-3': 'fadeUp 0.4s ease 0.19s both',
            'fade-up-4': 'fadeUp 0.4s ease 0.26s both',
            'fade-up-5': 'fadeUp 0.4s ease 0.33s both',
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-[#f8fafc] font-sans flex items-center justify-center px-4 relative overflow-hidden">

  <div class="absolute top-0 inset-x-0 h-2 bg-brand"></div>

  <div class="w-full max-w-md relative z-10">

    <div class="animate-fade-up-1 flex flex-col items-center mb-8">
      <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mb-4 shadow-xl shadow-brand/10 border border-slate-100 p-3">
        <img src="../static/imgs/logo-diente.png" alt="Logo Dentista" class="w-full h-full object-contain">
      </div>
      <h1 class="text-2xl font-bold text-brand tracking-wide">DENTISTA</h1>
      <p class="text-sm text-slate-400 mt-1 font-medium">Sistema de gestión clínica</p>
    </div>

    <div class="animate-fade-up-2 bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10">

      <h2 class="text-xl font-bold text-slate-800 mb-2 text-center">¡Bienvenido de nuevo!</h2>
      <p class="text-sm text-slate-500 mb-8 text-center">Ingrese sus credenciales para acceder</p>

      <form onsubmit="window.location.href='dashboard.php'; return false;" class="space-y-5">

        <div class="animate-fade-up-3">
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Usuario</label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
              <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </span>
            <input
              type="text"
              placeholder="Ej: admin.user"
              class="w-full pl-12 pr-4 py-3.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all font-medium"
              required
            />
          </div>
        </div>

        <div class="animate-fade-up-4">
          <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contraseña</label>
            <a href="#" class="text-xs font-semibold text-brand hover:text-brand-dark transition-colors">¿Olvidaste tu contraseña?</a>
          </div>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
              <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </span>
            <input
              id="passInput"
              type="password"
              placeholder="••••••••"
              class="w-full pl-12 pr-12 py-3.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all font-medium"
              required
            />
            <button
              type="button"
              onclick="togglePass()"
              tabindex="-1"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand transition-colors"
            >
              <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="animate-fade-up-5 pt-4">
          <button
            type="submit"
            class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark active:scale-[0.98] transition-all text-white text-sm font-bold py-4 rounded-xl shadow-lg shadow-brand/30"
          >
            Ingresar al sistema
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </button>
        </div>

      </form>
    </div>

    <p class="text-center text-xs font-medium text-slate-400 mt-8 animate-fade-up-5">
      &copy; 2026 Dentista. Acceso restringido.
    </p>

  </div>

  <script>
    function togglePass() {
      const input = document.getElementById('passInput');
      const icon  = document.getElementById('eyeIcon');
      const isPassword = input.type === 'password';
      
      input.type = isPassword ? 'text' : 'password';
      
      icon.innerHTML = isPassword
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
  </script>
</body>
</html>