<?php

declare(strict_types=1);

namespace Quillstack\DI\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
    //
}
