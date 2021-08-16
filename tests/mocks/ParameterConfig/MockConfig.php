<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\ParameterConfig;

final class MockConfig
{
    public string $test;

    public function __construct(string $test = 'default')
    {
        $this->test = !empty($test) ? $test : 'construct';
    }
}
