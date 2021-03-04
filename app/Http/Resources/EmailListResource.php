<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class EmailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_map(function($item) {
            foreach ($item['attachments'] as $key => $attachment) {
                $item['attachments'][$key]['link'] = url(Storage::url($attachment['path']));
            }
            return $item;
        },$this->resource);
    }
}
