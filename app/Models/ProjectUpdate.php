<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUpdate extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectUpdateFactory> */
    use HasFactory;

    /**
     * Get the project associated with the project update.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the updater user associated with the project update.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
