<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Properties;

use Quillstack\DI\Tests\Mocks\Database\MockDatabase;
use Quillstack\DI\Tests\Mocks\Simple\MockRepository;

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
