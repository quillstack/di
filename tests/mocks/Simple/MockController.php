<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Simple;

final class MockController
{
    public function __construct(public MockService $service)
    {
        //
    }
}
