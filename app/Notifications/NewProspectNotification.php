<?php

namespace App\Notifications;

use App\Models\Prospect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProspectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Prospect $prospect)
    {
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 300];
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo prospecto: '.$this->prospect->displayName())
            ->greeting('Nuevo formulario de contacto')
            ->line($this->prospect->displayName().' solicitó información desde el sitio.')
            ->line('Servicio: '.(config('sentriq.services.'.$this->prospect->service_interest.'.name') ?? 'Sin especificar'))
            ->line('Zona: '.($this->prospect->municipality ?: 'Sin especificar'))
            ->action('Revisar prospecto', route('admin.prospects.show', $this->prospect));
    }
}
