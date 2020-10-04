<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\ParameterConfig;

use PHPUnit\Framework\TestCase;

final class MockConfig
{
    public string $test;

    public function __construct(string $test = 'default')
    {
        $this->test = !empty($test) ? $test : 'construct';
    }
}
