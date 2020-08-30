<?php

declare(strict_types=1);

namespace QuillDIMocks\Database;

final class MockDatabaseController
{
    /**
     * @var MockDatabase
     */
    public MockDatabase $database;

    public function __construct(MockDatabase $database)
    {
        $this->database = $database;
    }
}
