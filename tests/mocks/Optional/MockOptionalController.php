<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Optional;

final class MockOptionalController
{
    public const NAME = 'test';
    public string $name;

    public function __construct(string $name = self::NAME)
    {
        $this->name = $name;
    }
}
