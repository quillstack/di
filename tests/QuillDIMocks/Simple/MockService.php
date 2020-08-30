<?php

declare(strict_types=1);

namespace QuillDIMocks\Simple;

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
