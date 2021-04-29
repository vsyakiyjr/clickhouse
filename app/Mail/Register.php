<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Register extends Notification
{
    use Queueable, SerializesModels;
    
    public $user;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via()
    {
        return 'mail';
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('ikeamaniaby@gmail.com')
            ->subject('IKEAMANIA')
            ->line('Чтобы начать работу, необходимо подтвердить электронный адрес.')
            ->action('Подтверждение', url("/activate?id=" . $this->user->id))
            ->line('Спасибо!')
            ->line('Команда Ikeamania');
    }
}
