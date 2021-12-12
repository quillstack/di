<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Database;

final class MockDatabaseController
{
    public function __construct(public MockDatabase $database)
    {
        //
    }
}
