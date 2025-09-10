<?php

namespace App\Traits;

trait HasArrayRepresentation
{
    public static function asArray(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = (object)['id' => $case->value, 'name' => $case->getLabel()];
        }
        return $values;
    }
}
