<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailChangeShipped extends Mailable
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
         return $this->subject(APP_NAME." メールアドレスの変更")
            ->text('emails.templates.mailaddress_change_mail');
     }
}
