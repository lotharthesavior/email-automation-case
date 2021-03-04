<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmailListResource;
use App\Http\Resources\EmailSendResource;
use App\Models\Email;
use Exception;
use App\Repositories\Attachments;
use App\Http\Requests\EmailListRequest;
use App\Http\Requests\EmailSendRequest;
use App\Jobs\SendEmail as SendEmailJob;
use App\Repositories\Emails;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class EmailController extends Controller
{
    /**
     * @var Emails
     */
    protected $emailsRepository;

    /**
     * @var Attachments
     */
    protected $attachmentRepository;

    public function __construct(Emails $emailsRepository, Attachments $attachmentRepository)
    {
        $this->emailsRepository     = $emailsRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * @param EmailSendRequest $request
     *
     * @return EmailSendResource|JsonResponse
     */
    public function send(EmailSendRequest $request)
    {
        $emailsSent = [];

        foreach ($request->all() as $email) {
            try {
                $emailsSent[] = $this->processSendEmail($email);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return new EmailSendResource($emailsSent);
    }

    /**
     * @param array $data
     *
     * @return Email
     *
     * @throws Exception
     */
    private function processSendEmail(array $data): Email
    {
        $email = $this->emailsRepository->create([
            'email'   => $data['email'],
            'subject' => $data['subject'],
            'body'    => $data['body'],
        ]);

        $attachments = [];
        if (isset($data['attachments'])) {
            $attachments = $this->attachmentRepository->create(array_merge($data['attachments'], [
                'email_id' => $email->id,
            ]));
        }

        dispatch(new SendEmailJob($email, $attachments));

        return $email;
    }

    /**
     * @param EmailListRequest $request
     *
     * @return EmailListResource
     */
    public function list(EmailListRequest $request)
    {
        $emails = Email::with('attachments')->get();

        return new EmailListResource($emails->toArray());
    }
}
