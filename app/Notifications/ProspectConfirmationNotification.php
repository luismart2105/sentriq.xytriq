<?php

namespace App\Notifications;

use App\Models\Prospect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProspectConfirmationNotification extends Notification implements ShouldQueue
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
            ->subject('Recibimos tu solicitud | Sentriq')
            ->greeting('Hola, '.$this->prospect->displayName())
            ->line('Gracias por contactar a Sentriq. Tu solicitud ya quedó registrada.')
            ->line('Revisaremos la información que nos compartiste para entender mejor lo que necesitas y definir contigo el siguiente paso.')
            ->line('Servicio: '.(config('sentriq.services.'.$this->prospect->service_interest.'.name') ?? 'Por definir'))
            ->line('Zona: '.($this->prospect->municipality ?: 'Por definir'))
            ->line('Te contactaremos dentro de 24 horas hábiles.')
            ->action('Agregar información por WhatsApp', config('sentriq.contact.whatsapp_url'))
            ->line('Si necesitas complementar tu solicitud con fotografías o algún detalle, puedes responder a este correo o escribirnos por WhatsApp.');
    }
}
