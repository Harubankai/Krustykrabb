<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The password reset code
     *
     * @var string
     */
    public $resetCode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $resetCode)
    {
        $this->resetCode = $resetCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Password Reset Request')
            ->markdown('emails.reset-password') // Uses the Markdown template
            ->with([
                'resetCode' => $this->resetCode,
            ]);
    }
}
