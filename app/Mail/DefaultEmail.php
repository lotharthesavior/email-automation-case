<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string */
    public $body;

    /** @var array */
    public $attachmentsArray;

    /**
     * Create a new message instance.
     *
     * @param string $body
     * @param array $attachments
     *
     * @return void
     */
    public function __construct(string $body, array $attachments)
    {
        $this->body        = $body;
        $this->attachmentsArray = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = $this->view('emails.default_email', [
            'body' => $this->body,
        ]);

        foreach ($this->attachmentsArray as $attachment) {
            $view->attachFromStorage($attachment->path, $attachment->name);
        }

        return $view;
    }
}
