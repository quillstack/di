<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Simple;

final class MockController
{
    public function __construct(public MockService $service)
    {
        //
    }
}
