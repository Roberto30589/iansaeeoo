<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotificationBase; // Import the base ResetPassword notification
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends ResetPasswordNotificationBase
{
    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(60),
            ['token' => $this->token, 'email' => $notifiable->email]
        );

        return (new MailMessage)
            ->subject(Lang::get('Restablecer contraseña'))
            ->line(Lang::get('Está recibiendo este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.'))
            ->action(Lang::get('Restablecer contraseña'), $url)
            ->line(Lang::get('Este enlace de restablecimiento de contraseña caducará en 60 minutos.'))
            ->line(Lang::get('Si no solicitó un restablecimiento de contraseña, no se requiere ninguna otra acción.'));
    }
}
