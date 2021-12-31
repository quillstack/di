<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Simple;

final class MockService
{
    public function __construct(public MockRepository $repository)
    {
        //
    }
}
