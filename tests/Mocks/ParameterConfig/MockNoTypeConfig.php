<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\ParameterConfig;

final class MockNoTypeConfig
{
    /**
     * @var mixed|null
     */
    public $content;

    public function __construct($content = 'default')
    {
        $this->content = $content;
    }
}
