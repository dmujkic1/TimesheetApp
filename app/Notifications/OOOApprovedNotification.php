<?php

namespace App\Notifications;

use App\Models\OOO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class OOOApprovedNotification extends Notification
{
    use Queueable, Notifiable;
    
    protected $ooo;
    /**
     * Create a new notification instance.
     */
    public function __construct(OOO $ooo)
    {
        $this->ooo=$ooo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->ooo->date ? 
                'Vaš OOO unos na dan ' . $this->ooo->date->format('d.m.Y') . ' je odobren.' :
                'Vaš OOO unos je odobren, ali datum nije dostupan.',
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Vaš OOO zahtjev za datum {$this->ooo->start_date} do {$this->ooo->end_date} je odobren.",
        ];
    }
}
