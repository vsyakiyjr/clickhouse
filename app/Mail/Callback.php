<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Callback extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $details;

    /**
     * Callback constructor.
     * @param array $details
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('SUPPORT_EMAIL', 'ikeamaniaby@gmail.com'))
            ->to(env('SUPPORT_EMAIL', 'ikeamaniaby@gmail.com'))
            ->subject('Заказ дзвонка')
            ->view('emails.callback')
            ->with('details', $this->details);
    }
}
