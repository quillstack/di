<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Database;

final class MockDatabaseController
{
    public function __construct(public MockDatabase $database)
    {
        //
    }
}
