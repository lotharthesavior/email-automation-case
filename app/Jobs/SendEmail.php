<?php

namespace App\Jobs;

use App\Models\Email;
use Exception;
use App\Mail\DefaultEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Lcobucci\JWT\Signer\EcdsaTest;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $email;

    /** @var string */
    protected $subject;

    /** @var string */
    protected $body;

    /**
     * Expected format: ['name' => string, 'value' => string]
     *
     * @var array
     */
    protected $attachments;

    /**
     * Create a new job instance.
     *
     * @param Email $email
     * @param array $attachments
     *
     * @return void
     */
    public function __construct(Email $email, array $attachments)
    {
        $this->email       = $email;
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle()
    {
        $mailable = new DefaultEmail($this->email->body, $this->attachments);

        $mailable->subject($this->email->subject);

        foreach ($this->attachments as $attachment) {
            $mailable->attachFromStorage($attachment->path, $attachment->name);
        }

        Mail::to($this->email->email)->send($mailable);
    }

    /**
     * @param DefaultEmail $mailable By reference
     *
     * @throws Exception
     */
    public function attachFiles(DefaultEmail &$mailable)
    {
        foreach ($this->attachments as $attachment) {
            if (!isset($attachment['value']) || !isset($attachment['name'])) {
                throw new Exception('Attachments have missing information, expected format: ["name" => string, "value" => string]');
            }

            if (!file_exists($attachment['value'])) {
                throw new Exception('Attachment\'s file doesn\'t exist');
            }

            $mailable->attachFromStorage($attachment['value'], $attachment['name']);
        }
    }
}
