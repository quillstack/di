<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Optional;

/**
 * The shape a package uses to say a collaborator may be left out: an interface, nullable,
 * defaulting to null. `Quillstack\Queue\Queues\DatabaseQueue` and `FileQueue` both take a
 * PSR-20 clock this way.
 */
final class MockOptionalCollaborator
{
    public function __construct(public readonly ?MockCollaborator $collaborator = null)
    {
        //
    }
}
