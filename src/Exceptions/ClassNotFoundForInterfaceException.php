<?php

declare(strict_types=1);

namespace Quillstack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ClassNotFoundForInterfaceException extends ContainerException implements NotFoundExceptionInterface
{
    //
}
