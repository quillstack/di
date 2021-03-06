<?php

namespace QuillStack\Mocks\DI\Optional;

final class MockOptionalController
{
    public const NAME = 'test';

    public string $name;

    public function __construct(string $name = self::NAME)
    {
        $this->name = $name;
    }
}
