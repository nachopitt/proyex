<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'active',
        'user_id',
    ];

    /**
     * Get the user associated with the user profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
