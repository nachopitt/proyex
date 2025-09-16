<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'current_status',
        'current_progress_percentage',
        'start_date',
        'due_date',
        'end_date',
        'parent_id',
        'reporter_user_id',
        'assigned_user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'current_status' => Status::class,
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Get the project updates associated with the project.
     */
    public function projectUpdates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    /**
     * Get the parent project associated with the project.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    /**
     * Get the children projects associated with the project.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_id');
    }

    /**
     * Get the reporter user associated with the project.
     */
    public function reporterUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned user associated with the project.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags associated with the project.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Get the project's priority label.
     */
    protected function priorityLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->priority?->getLabel()
        );
    }

    /**
     * Get the project's current status label.
     */
    protected function currentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->current_status?->getLabel()
        );
    }

    /**
     * Dynamically add appends
     */
    public function getArrayableAppends(): array
    {
        $appends = parent::getArrayableAppends();

        if ($this->priority !== null) {
            $appends[] = 'priority_label';
        }

        if ($this->current_status !== null) {
            $appends[] = 'current_status_label';
        }

        return $appends;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Project>  $query
     * @param  array<string, string>  $filters
     * @return \Illuminate\Database\Eloquent\Builder<Project>
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        return $query;
    }
}
