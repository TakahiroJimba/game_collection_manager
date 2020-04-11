<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $sendData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sendData)
    {
        $this->sendData = $sendData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         return $this->subject(APP_NAME." 仮登録完了のお知らせ")
            ->text('emails.templates.registers_mail');
     }
}
