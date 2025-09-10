<?php

namespace App\Traits;

use App\Contracts\Stateful;

trait HasStateMachine
{
    public function canTransitionTo(Stateful $next): bool
    {
        if (get_class($this) !== get_class($next)) {
            return false;
        }

        return in_array($next->value, $this->transitions()[$this->value] ?? []);
    }
}
