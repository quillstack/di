<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Simple;

final class MockService
{
    public function __construct(public MockRepository $repository)
    {
        //
    }
}