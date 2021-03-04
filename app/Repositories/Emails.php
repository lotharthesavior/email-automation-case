<?php

namespace App\Repositories;

use Exception;
use App\Models\Email;

class Emails
{
    /**
     * @param array $data
     *
     * @return Email
     *
     * @throws Exception
     */
    public function create(array $data): Email
    {
        $email = Email::create(array_merge($data, [
            'user_id' => auth()->user()->id,
        ]));

        if ($email instanceof Email) {
            return $email;
        }

        throw new Exception('There was an error while trying to register an Email.');
    }
}
