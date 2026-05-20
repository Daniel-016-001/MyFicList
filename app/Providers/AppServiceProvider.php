<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifica tu correo electrónico - MyFicList')
                ->greeting('¡Hola, ' . ($notifiable->username ?? $notifiable->name) . '!')
                ->line('¡Te damos la bienvenida a MyFicList!')
                ->line('Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico y comenzar a organizar tus lecturas, series y películas favoritas.')
                ->action('Verificar mi cuenta', $url)
                ->line('Si no te has registrado en nuestra web, puedes ignorar este correo.')
                ->salutation('El equipo de MyFicList');
        });
    }
}

