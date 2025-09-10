<?php

namespace App\Enums;

use App\Contracts\Labelable;
use App\Traits\HasArrayRepresentation;

enum Role: string implements Labelable
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
