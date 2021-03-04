<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'path', 'name', 'email_id',
    ];

    public function email()
    {
        return $this->hasOne(Email::class);
    }
}
