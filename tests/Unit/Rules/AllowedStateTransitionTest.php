<?php

namespace Tests\Unit\Rules;

use App\Rules\AllowedStateTransition;
use App\Enums\Status;
use Tests\TestCase;

class AllowedStateTransitionTest extends TestCase
{
    /**
     * Test a valid transition is allowed.
     */
    public function test_valid_transition_passes(): void
    {
        $rule = new AllowedStateTransition(Status::class, Status::PLANNED);

        $passed = true;
        $rule->validate('status', Status::IN_PROGRESS->value, function (string $message) use (&$passed) {
            $passed = false;
        });

        $this->assertTrue($passed);
    }

    /**
     * Test an invalid transition is blocked.
     */
    public function test_invalid_transition_fails(): void
    {
        $rule = new AllowedStateTransition(Status::class, Status::CANCELLED);

        $failedMessage = null;
        $rule->validate('status', Status::IN_PROGRESS->value, function (string $message) use (&$failedMessage) {
            $failedMessage = $message;
        });

        $this->assertNotNull($failedMessage);
        $this->assertStringContainsString('is not allowed', $failedMessage);
    }

    /**
     * Test transition from null (creation) to initial state.
     */
    public function test_initial_state_transition_passes(): void
    {
        $rule = new AllowedStateTransition(Status::class, null);

        $passed = true;
        $rule->validate('status', Status::PLANNED->value, function (string $message) use (&$passed) {
            $passed = false;
        });

        $this->assertTrue($passed);
    }

    /**
     * Test transition from null to non-initial state fails.
     */
    public function test_transition_to_non_initial_fails_on_creation(): void
    {
        $rule = new AllowedStateTransition(Status::class, null);

        $failedMessage = null;
        $rule->validate('status', Status::COMPLETED->value, function (string $message) use (&$failedMessage) {
            $failedMessage = $message;
        });

        $this->assertNotNull($failedMessage);
        $this->assertStringContainsString('The initial state must be Planned', $failedMessage);
    }
}
