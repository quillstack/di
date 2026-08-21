<?php

declare(strict_types=1);

namespace Quillstack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class UnableToCreateReflectionClassException extends ContainerException implements NotFoundExceptionInterface
{
    //
}
