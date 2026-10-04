<?php

namespace App\Notifications;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPartnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Agency $agency)
    {
        //
    }

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
        $agency = $this->agency->fresh('user');

        return (new MailMessage)
            ->subject('Nouvelle demande de partenaire - AB-Flash')
            ->greeting('Bonjour Administrateur,')
            ->line("Une nouvelle agence a demandé à rejoindre AB-Flash :")
            ->line("**Nom :** {$agency->name}")
            ->line("**Email :** {$agency->user->email}")
            ->line("**Adresse :** {$agency->address}")
            ->line("**WhatsApp :** {$agency->whatsapp}")
            ->action('Voir les demandes', url('/admin/agencies'))
            ->line('Merci d\'utiliser AB-Flash.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'agency_id' => $this->agency->id,
            'agency_name' => $this->agency->name,
        ];
    }
}
