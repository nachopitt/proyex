<?php

namespace App\Contracts;

interface Stateful
{
    /**
     * Get the state transition map.
     *
     * @return array
     */
    public function transitions(): array;

    /**
     * Get the human-readable label for the enum case.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Check if a transition to a given state is allowed.
     *
     * @param self $next
     * @return bool
     */
    public function canTransitionTo(self $next): bool;
}
