<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\ParameterConfig;

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
