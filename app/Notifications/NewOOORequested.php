<?php

namespace App\Notifications;

use App\Models\OOO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOOORequested extends Notification
{
    use Queueable;
    public OOO $ooo;

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
    public function via($notifiable): array
    {
        return ['database'];
    }


    public function toDataBase($notifiable)
    {
        return [
            'title' => 'Novi OOO zahtjev',
            'message' => $this->ooo->user->name . ' je poslao/la OOO zahtjev.',
            'ooo_id' => $this->ooo->id
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
            'message' => "Novi OOO zahtjev od korisnika {$this->ooo->user->name} ({$this->ooo->start_date} - {$this->ooo->end_date})",
        ];
    }
}
