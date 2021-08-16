<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Logger;

interface MockLoggerInterface
{
    public function info(string $message);
}
