<?php

namespace App\Enums;

enum Status: int
{
    case PLANNED = 1;
    case IN_PROGRESS = 2;
    case COMPLETED = 3;
    case ON_HOLD = 4;
    case CANCELLED = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::PLANNED => 'Planned',
            self::IN_PROGRESS => 'In progress',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On hold',
            self::CANCELLED => 'Cancelled',
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
