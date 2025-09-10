<?php

namespace App\Enums;

use App\Contracts\HasLabel;
use App\Traits\HasArrayRepresentation;

enum Priority: int implements HasLabel
{
    use HasArrayRepresentation;

    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }
}
