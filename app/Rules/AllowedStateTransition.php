<?php

namespace App\Rules;

use App\Contracts\Stateful;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Lang;

class AllowedStateTransition implements ValidationRule
{
    protected $fromState;
    protected string $enumClass;
    protected ?string $message;

    public function __construct(string $enumClass, $fromState, ?string $message = null)
    {
        $this->enumClass = $enumClass;
        $this->fromState = $fromState;
        $this->message = $message;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_subclass_of($this->enumClass, Stateful::class)) {
            $fail('The validation rule is misconfigured for the given enum.');
            return;
        }

        $toState = $this->enumClass::tryFrom($value);

        if (!$toState) {
            $fail('The selected state is not valid.');
            return;
        }

        // If there is no initial state, the transition is allowed.
        if (!$this->fromState) {
            return;
        }

        if ($this->fromState->canTransitionTo($toState)) {
            return;
        }

        // If the transition is not allowed, construct and fail with the message.
        $default = 'Transition from :old to :new is not allowed.';
        $message = $this->message ?? $default;

        if (Lang::has($message)) {
            $message = Lang::get($message);
        }

        $message = str_replace(
            [':old', ':new'],
            [$this->fromState->getLabel(), $toState->getLabel()],
            $message
        );

        $fail($message);
    }
}
