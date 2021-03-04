<?php

namespace App\Repositories;

use Exception;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class Attachments
{
    /**
     * @param array $data
     *
     * @return Attachment[]
     *
     * @throws Exception
     */
    public function create(array $data): array
    {
        $email_id = $data['email_id'];
        unset($data['email_id']);

        $files = [];
        foreach ($data as $single) {
            $files[ $single['name'] ] = $this->storeFilesystem($single);
        }

        $attachments = [];
        foreach ($files as $name => $path) {
            $attachments[] = Attachment::create([
                'name'     => $name,
                'path'     => $path,
                'email_id' => $email_id,
            ]);
        }

        $attachments = array_filter($attachments, function($attachment){
            return $attachment instanceof Attachment;
        });

        if (!empty($attachments)) {
            return $attachments;
        }

        throw new Exception('There was an error while trying to register an Attachment.');
    }

    /**
     * @param array $data
     *
     * @return string
     *
     * @throws Exception
     */
    private function storeFilesystem(array $data): string
    {
        $ext = pathinfo($data['name'], PATHINFO_EXTENSION);
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString() . '.' . $ext;
        $imgdata = base64_decode($data['value']);
        file_put_contents($tmpFilePath, $imgdata);

        $tmpFile = new File($tmpFilePath);
        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true
        );

        return $file->storePublicly('/public');
    }
}
