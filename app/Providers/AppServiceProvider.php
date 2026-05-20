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
            $username = $notifiable->username ?? $notifiable->name;
            $emailAddress = $notifiable->getEmailForVerification();

            $htmlContent = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; background-color: #ffffff;\">
                <h2 style=\"color: #1f2937;\">¡Hola, {$username}!</h2>
                <p style=\"color: #4b5563; font-size: 16px; line-height: 1.5;\">¡Te damos la bienvenida a MyFicList!</p>
                <p style=\"color: #4b5563; font-size: 16px; line-height: 1.5;\">Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico y comenzar a organizar tus lecturas, series y películas favoritas.</p>
                <div style=\"text-align: center; margin: 30px 0;\">
                    <a href=\"{$url}\" style=\"background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;\">Verificar mi cuenta</a>
                </div>
                <p style=\"color: #6b7280; font-size: 14px; line-height: 1.5; border-top: 1px solid #e5e7eb; padding-top: 20px;\">Si no te has registrado en nuestra web, puedes ignorar este correo.</p>
                <p style=\"color: #374151; font-weight: bold; margin-top: 20px;\">El equipo de MyFicList</p>
            </div>
            ";

            try {
                $fromAddress = config('mail.from.address') ?: 'hello@myficlist.com';
                $fromName = config('mail.from.name') ?: 'MyFicList';

                $email = (new MailtrapEmail())
                    ->from(new Address($fromAddress, $fromName))
                    ->to(new Address($emailAddress))
                    ->subject('Verifica tu correo electrónico - MyFicList')
                    ->category('Verify Email')
                    ->html($htmlContent)
                ;

                MailtrapClient::initSendingEmails(
                    apiKey: env('SMTP')
                )->send($email);
            } catch (\Exception $e) {
                report($e);
            }

            return (new MailMessage)
                ->mailer('log')
                ->subject('Verifica tu correo electrónico - MyFicList');
        });
    }
}

