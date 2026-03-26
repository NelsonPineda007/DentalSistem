<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DENTISTA — Crear Nueva Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Poppins', 'sans-serif'], },
          colors: {
            brand: { DEFAULT: '#1e40af', dark: '#1e3a8a', light: '#eff6ff', }
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
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
      </div>

      <h2 class="text-xl font-bold text-slate-800 mb-2 text-center">Nueva Contraseña</h2>
      <p class="text-sm text-slate-500 mb-6 text-center">Ingresa y confirma tu nueva contraseña segura.</p>

      {{-- ALERTA DE ERRORES --}}
      @if ($errors->any())
        <div class="mb-6 p-3 bg-red-50 border border-red-100 text-red-600 text-xs font-bold text-center rounded-xl">
            {{ $errors->first() }}
        </div>
      @endif

      {{-- FORMULARIO --}}
      <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
        @csrf

        {{-- TOKEN INVISIBLE --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- CORREO (Solo lectura) --}}
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
          <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly
            class="w-full px-4 py-3 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed font-medium" />
        </div>

        {{-- NUEVA CONTRASEÑA --}}
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nueva Contraseña</label>
          <div class="relative">
            <input id="passNueva" type="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8"
              class="w-full pl-4 pr-12 py-3 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all font-medium" />
            <button type="button" onclick="togglePass('passNueva', 'eyeIconNueva')" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand transition-colors">
              <svg id="eyeIconNueva" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Confirmar Contraseña</label>
          <div class="relative">
            <input id="passConfirma" type="password" name="password_confirmation" placeholder="Repite la contraseña" required minlength="8"
              class="w-full pl-4 pr-12 py-3 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all font-medium" />
            <button type="button" onclick="togglePass('passConfirma', 'eyeIconConfirma')" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand transition-colors">
              <svg id="eyeIconConfirma" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="pt-4">
          <button type="submit" class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark active:scale-[0.98] transition-all text-white text-sm font-bold py-3.5 rounded-xl shadow-lg shadow-brand/30">
            Guardar y Entrar
          </button>
        </div>
      </form>

    </div>
  </div>

  <script>
    function togglePass(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(iconId);
      const isPassword = input.type === 'password';
      
      input.type = isPassword ? 'text' : 'password';
      
      icon.innerHTML = isPassword
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
  </script>
</body>
</html>