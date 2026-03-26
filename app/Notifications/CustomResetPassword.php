<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Construye el enlace de recuperación con el token y el correo del usuario
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Apunta a la vista HTML que crearemos en el siguiente paso
        return (new MailMessage)
            ->subject('Recuperación de Contraseña - DENTISTA')
            ->view('emails.recuperar-clave', [
                'url' => $url,
                'usuario' => $notifiable->nombre
            ]);
    }
}