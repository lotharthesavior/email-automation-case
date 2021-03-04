<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Email extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'email', 'subject', 'body', 'user_id',
    ];

    /**
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
