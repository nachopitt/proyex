<?php

namespace App\Enums;

use App\Contracts\HasLabel;
use App\Traits\HasArrayRepresentation;

enum Role: string implements HasLabel
{
    use HasArrayRepresentation;

    case ADMIN = 'admin';
    case USER = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }
}
