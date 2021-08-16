<?php

namespace Quillstack\Mocks\DI\Database;

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
