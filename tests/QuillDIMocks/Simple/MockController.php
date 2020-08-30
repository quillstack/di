<?php

declare(strict_types=1);

namespace QuillDIMocks\Simple;

final class MockController
{
    /**
     * @var MockService
     */
    public MockService $service;

    public function __construct(MockService $service)
    {
        $this->service = $service;
    }
}
