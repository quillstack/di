<?php

namespace QuillStack\Mocks\DI\Database;

final class MockDatabaseController
{
    public function __construct(public MockDatabase $database)
    {
    }
}
