<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\ParameterConfig;

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
