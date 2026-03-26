<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class PasswordResetController extends Controller
{
    // 1. Muestra la pantalla para pedir el correo
    public function showLinkRequestForm() {
        return view('olvide-password'); 
    }

    // 2. Genera el Token y envía el Email
    public function sendResetLinkEmail(Request $request) {
        $request->validate(['email' => 'required|email|exists:empleados,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => '¡Te hemos enviado un enlace de recuperación al correo!'])
                    : back()->withErrors(['email' => 'Hubo un error al intentar enviar el correo.']);
    }

    // 3. Muestra la pantalla para escribir la nueva clave (viene desde el correo)
    public function showResetForm(Request $request, $token = null) {
        return view('resetear-password')->with(['token' => $token, 'email' => $request->email]);
    }

    // 4. Guarda la nueva contraseña en tu base de datos
    public function reset(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // Exige confirmación de clave
        ]);

$status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // Encriptamos con Bcrypt a tu columna real
                $user->password_hash = Hash::make($password); 
                $user->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect('/')->with('status', '¡Tu contraseña ha sido restablecida exitosamente!')
                    : back()->withErrors(['email' => 'El token de seguridad es inválido o ha expirado.']);
    }
}