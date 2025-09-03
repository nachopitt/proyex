<?php

namespace App\Enums;

enum Role: int
{
    case ADMIN = 1;
    case USER = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }

    public static function asArray(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = (object)['id' => $case->value, 'name' => $case->getLabel()];
        }
        return $values;
    }
}
