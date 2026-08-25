<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Optional;

use RuntimeException;

/**
 * Registered, and throws while being built. Nothing to do with a missing definition, so the
 * container must not quietly hand back a default instead.
 */
final class MockBrokenCollaborator implements MockCollaborator
{
    public function __construct()
    {
        throw new RuntimeException('this collaborator is broken');
    }

    public function name(): string
    {
        return 'never reached';
    }
}
