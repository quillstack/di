<?php

namespace Quillstack\Mocks\DI\Properties;

use Quillstack\Mocks\DI\Database\MockDatabase;
use Quillstack\Mocks\DI\Simple\MockRepository;

final class MockProperties
{
    public MockDatabase $database;
    public $logger;
    public ?MockRepository $repository;
    public array $test = [1, 2, 3];
    public ?int $number;
    private string $word;

    /**
     * @return MockDatabase
     */
    public function getDatabase(): MockDatabase
    {
        return $this->database;
    }
}
