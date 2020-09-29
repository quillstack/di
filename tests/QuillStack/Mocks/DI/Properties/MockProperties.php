<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\Properties;

use QuillStack\Mocks\DI\Database\MockDatabase;
use QuillStack\Mocks\DI\Logger\MockLogger;
use QuillStack\Mocks\DI\Simple\MockRepository;

final class MockProperties
{
    public MockDatabase $database;
    public MockLogger $logger;
    public ?MockRepository $repository;
    public array $test = [1, 2, 3];
    public ?int $number;
    public string $word;

    /**
     * @return MockDatabase
     */
    public function getDatabase(): MockDatabase
    {
        return $this->database;
    }
}
