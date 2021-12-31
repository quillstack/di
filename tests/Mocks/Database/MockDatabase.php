<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Database;

final class MockDatabase
{
    public function __construct(
        public string $hostname,
        public string $user,
        public string $password,
        public string $database
    ) {
    }
}
