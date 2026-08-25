<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Optional;

final class MockRealCollaborator implements MockCollaborator
{
    public function name(): string
    {
        return 'the real one';
    }
}
