<?php

namespace App\Enums;

use App\Contracts\Labelable;
use App\Traits\HasArrayRepresentation;

enum Priority: string implements Labelable
{
    use HasArrayRepresentation;

    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }
}
