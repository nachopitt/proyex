<?php

namespace App\Enums;

enum Priority: int
{
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

    public static function asArray(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = (object)['id' => $case->value, 'name' => $case->getLabel()];
        }
        return $values;
    }
}
