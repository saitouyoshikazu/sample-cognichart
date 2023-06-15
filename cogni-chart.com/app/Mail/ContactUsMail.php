<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    private $yourEmailAddress;
    private $mailSubject;
    private $bodyOfEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $yourEmailAddress, string $mailSubject, string $bodyOfEmail)
    {
        $this->yourEmailAddress = $yourEmailAddress;
        $this->mailSubject = $mailSubject;
        $this->bodyOfEmail = $bodyOfEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->mailSubject)
            ->text('www.statics.contactus.contactusmail')
            ->with([
                'yourEmailAddress'  =>  $this->yourEmailAddress,
                'bodyOfEmail'  =>  $this->bodyOfEmail
            ]);
    }
}
