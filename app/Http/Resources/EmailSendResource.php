<?php

namespace App\Http\Resources;

use App\Models\Email;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class EmailSendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_map(function($item){
            $item['attachments'] = Email::find($item['id'])->attachments;
            return $item;
        }, $this->resource);
    }
}
