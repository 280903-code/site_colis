<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerEmailVerificationNotification extends Notification
{
    public function __construct(
        public string $contactName,
        public string $verificationUrl,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmez votre adresse e-mail - KONEXA')
            ->greeting("Bonjour {$this->contactName},")
            ->line('Confirmez votre adresse e-mail pour valider votre demande de partenariat.')
            ->action('Confirmer mon adresse e-mail', $this->verificationUrl)
            ->line('Ce lien est valable pendant 24 heures.')
            ->line('Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer cet e-mail.');
    }
}
