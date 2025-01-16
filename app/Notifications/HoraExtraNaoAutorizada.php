<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HoraExtraNaoAutorizada extends Notification
{
    use Queueable;

    private $dados;

    public function __construct($dados)
    {
        $this->dados = $dados;
        
    }

    /**
     * Create a new notification instance.
     */
  

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
            ->subject('Hora Extra Não Autorizada')
            ->line("O funcionário {$this->dados['funcionario']} registrou {$this->dados['minutos']} minutos extras não autorizados.")
            ->line("Data: {$this->dados['data']}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            
        ];
    }
}
