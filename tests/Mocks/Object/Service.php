<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Object;

class Service
{
    public Logger $logger;
    public LoggerInterface $loggerFromInterface;
}
