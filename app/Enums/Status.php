<?php

namespace App\Enums;

use App\Contracts\Labelable;
use App\Contracts\Stateful;
use App\Traits\HasArrayRepresentation;
use App\Traits\HasStateMachine;

enum Status: string implements Labelable, Stateful
{
    use HasArrayRepresentation, HasStateMachine;

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

    public static function transitions(): array
    {
        return [
            self::PLANNED->value => [self::IN_PROGRESS->value, self::CANCELLED->value],
            self::IN_PROGRESS->value => [self::ON_HOLD->value, self::COMPLETED->value, self::CANCELLED->value],
            self::ON_HOLD->value => [self::IN_PROGRESS->value, self::CANCELLED->value],
            self::COMPLETED->value => [],
            self::CANCELLED->value => [],
        ];
    }

    public static function getInitialState(): self
    {
        return self::PLANNED;
    }
}
