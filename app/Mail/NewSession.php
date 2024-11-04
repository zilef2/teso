<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewSession extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private mixed $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Login',
        );
    }

//    public function content(): Content
//    {
//        return new Content(
//            view: 'view.name',
//        );
//    }

    public function build()
    {
        return $this->view('mails.newsession')->with([
            'name' => $this->userName,
        ]);
    }
}
