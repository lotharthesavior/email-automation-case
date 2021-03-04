<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Jobs\SendEmail as SendEmailJob;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send
                            {email: The email to send to}
                            {subject: The subject of this email}
                            {body: The body of this email}
                            {attachments: Attachments comma separated, e.g.: name:file_path,name:file_path...}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch an email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        try {
            $attachments = $this->parseAttachments($this->argument('attachments'));
        } catch (Exception $e) {
            $this->info('Error: ' . $e->getMessage());
            return;
        }

        dispatch(new SendEmailJob(
            $this->argument('email'),
            $this->argument('subject'),
            $this->argument('body'),
            $attachments,
        ));
    }


    private function parseAttachments(string $attachments): array
    {
        $parsedAttachments = explode(',', $attachments);

        return array_map(function($attachment){
            $parts = explode(':', $attachment);

            if (count($parts) !== 2) {
                throw new Exception('Wrong number of parameters for attachments.');
            }

            if (empty($parts[0])) {
                throw new Exception('Attachment must have a non-empty name.');
            }

            if (!file_exists(storage_path($parts[1]))) {
                throw new Exception('Attachment must have an existent file path available at the storage.');
            }

            return [
                'name' => $parts[0],
                'path' => $parts[1],
            ];
        }, $parsedAttachments);
    }
}
