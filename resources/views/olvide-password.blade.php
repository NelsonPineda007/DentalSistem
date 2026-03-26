<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DENTISTA — Recuperar Contraseña</title>
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
              DEFAULT: '#1e40af', 
              dark:    '#1e3a8a', 
              light:   '#eff6ff', 
            }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-[#f8fafc] font-sans flex items-center justify-center px-4 relative overflow-hidden">

  <div class="absolute top-0 inset-x-0 h-2 bg-brand"></div>

  <div class="w-full max-w-md relative z-10">

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10">
      
      <div class="flex justify-center mb-6">
        <div class="w-16 h-16 bg-brand-light rounded-2xl flex items-center justify-center text-brand">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>
      </div>

      <h2 class="text-xl font-bold text-slate-800 mb-2 text-center">Recuperar Contraseña</h2>
      <p class="text-sm text-slate-500 mb-6 text-center">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

      {{-- MENSAJE DE ÉXITO AL ENVIAR EL CORREO --}}
      @if (session('status'))
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold text-center rounded-xl">
            {{ session('status') }}
        </div>
      @endif

      {{-- ALERTA DE ERRORES --}}
      @if ($errors->any())
        <div class="mb-6 p-3 bg-red-50 border border-red-100 text-red-600 text-xs font-bold text-center rounded-xl">
            {{ $errors->first() }}
        </div>
      @endif

      {{-- FORMULARIO --}}
      <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Correo Electrónico</label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
              <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </span>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="ejemplo@dentalsistem.com"
              class="w-full pl-12 pr-4 py-3.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all font-medium"
              required
            />
          </div>
        </div>

        <div class="pt-4 flex flex-col gap-3">
          <button
            type="submit"
            class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark active:scale-[0.98] transition-all text-white text-sm font-bold py-3.5 rounded-xl shadow-lg shadow-brand/30"
          >
            Enviar enlace
          </button>
          
          <a href="{{ route('login') }}" class="w-full text-center text-sm font-semibold text-slate-500 hover:text-brand transition-colors py-2">
            Volver al inicio de sesión
          </a>
        </div>

      </form>
    </div>
  </div>

</body>
</html>