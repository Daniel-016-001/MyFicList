<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-mail', function () {
    $username = 'Usuario de Prueba';
    $url = 'https://myficlist.com/email/verify/mock-url-token';

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

    $fromAddress = config('mail.from.address') ?: 'hello@myficlist.com';
    $fromName = config('mail.from.name') ?: 'MyFicList';

    $email = (new \Mailtrap\Mime\MailtrapEmail())
        ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
        ->to(new \Symfony\Component\Mime\Address('myficlist@gmail.com'))
        ->subject('Verifica tu correo electrónico - MyFicList')
        ->category('Verify Email')
        ->html($htmlContent)
    ;

    $this->info('Sending verification test email using Mailtrap SDK...');

    $response = \Mailtrap\MailtrapClient::initSendingEmails(
        apiKey: env('SMTP')
    )->send($email);

    $this->info('Response from Mailtrap:');
    var_dump(\Mailtrap\Helper\ResponseHelper::toArray($response));
})->purpose('Send verification test mail using Mailtrap SDK');

