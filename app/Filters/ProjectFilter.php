<?php

namespace App\Filters;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProjectFilter
{
    protected array $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

    public function apply(Builder $query): Builder
    {
        $query->when($this->filters['status'] ?? false, function ($q, $status) {
            return $this->status($q, $status);
        });

        $query->when($this->filters['priority'] ?? false, function ($q, $priority) {
            return $this->priority($q, $priority);
        });

        $query->when($this->filters['tags'] ?? false, function ($q, $tags) {
            return $this->tags($q, $tags);
        });

        $query->when($this->filters['assignee'] ?? false, function ($q, $assignee) {
            return $this->assignee($q, $assignee);
        });

        $query->when($this->filters['due_date'] ?? false, function ($q, $dueDate) {
            return $this->dueDate($q, $dueDate);
        });

        $query->when($this->filters['sort_by'] ?? false, function ($q, $sortBy) {
            return $this->sort($q, $sortBy, $this->filters['sort_direction'] ?? 'asc');
        });

        $query->when($this->filters['search'] ?? false, function ($q, $search) {
            return $this->search($q, $search);
        });

        return $query;
    }

    protected function search(Builder $query, string $search): Builder
    {
        // Assume a minimum word length of 3 for the '+' operator to avoid issues with stopwords
        $minWordLen = 3;

        $searchQuery = collect(explode(' ', $search))->map(function ($term) use ($minWordLen) {
            if (strlen($term) >= $minWordLen) {
                return '+' . $term . '*'; // Must be present
            }
            return $term . '*'; // Optional
        })->implode(' ');

        return $query->where(function (Builder $q) use ($searchQuery) {
            $q->whereFullText(['title', 'description'], $searchQuery, ['mode' => 'boolean'])
                ->orWhereHas('projectUpdates', function (Builder $q) use ($searchQuery) {
                    $q->whereFullText('description', $searchQuery, ['mode' => 'boolean']);
                });
        });
    }

    protected function status(Builder $query, string $status): Builder
    {
        return $query->where('current_status', $status);
    }

    protected function priority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    protected function tags(Builder $query, array $tags): Builder
    {
        return $query->whereHas('tags', function (Builder $q) use ($tags) {
            $q->whereIn('tags.id', $tags);
        });
    }

    protected function assignee(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_user_id', $userId);
    }

    protected function dueDate(Builder $query, string $range): Builder
    {
        if ($range === 'overdue') {
            return $query->where('due_date', '<', Carbon::today())->whereNotIn('status', [Status::COMPLETED]);
        }
        if ($range === 'today') {
            return $query->whereDate('due_date', Carbon::today());
        }
        if ($range === 'week') {
            return $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addWeek()]);
        }
        return $query;
    }

    protected function sort(Builder $query, string $field, string $direction): Builder
    {
        $sortable = ['name', 'created_at', 'due_date', 'priority', 'status'];

        if (in_array($field, $sortable)) {
            return $query->orderBy($field, $direction);
        }
        return $query;
    }
}
