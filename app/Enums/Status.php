<?php

namespace App\Enums;

use App\Contracts\HasLabel;
use App\Traits\HasArrayRepresentation;

enum Status: int implements HasLabel
{
    use HasArrayRepresentation;

    case PLANNED = 1;
    case IN_PROGRESS = 2;
    case ON_HOLD = 3;
    case COMPLETED = 4;
    case CANCELLED = 5;

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
