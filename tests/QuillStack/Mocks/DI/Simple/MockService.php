<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\Simple;

final class MockService
{
    /**
     * @var MockRepository
     */
    public MockRepository $repository;

    public function __construct(MockRepository $repository)
    {
        $this->repository = $repository;
    }
}
