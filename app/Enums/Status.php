<?php

namespace App\Enums;

use App\Contracts\Labelable;
use App\Traits\HasArrayRepresentation;

enum Status: string implements Labelable
{
    use HasArrayRepresentation;

    case PLANNED = 'planned';
    case IN_PROGRESS = 'in-progress';
    case ON_HOLD = 'on-hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PLANNED => 'Planned',
            self::IN_PROGRESS => 'In progress',
            self::ON_HOLD => 'On hold',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
