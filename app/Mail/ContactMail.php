<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $userMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $userMessage)
    {
        $this->name = $name;
        $this->email = $email;
        $this->userMessage = $userMessage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Inquiry to Krusty Krab')
            ->from($this->email, $this->name)
            ->markdown('emails.contact')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage,
            ]);
    }
}
