<?php

declare(strict_types=1);

namespace QuillStack\DI\Exceptions;

use Psr\Container\ContainerExceptionInterface;

final class UnableToCreateReflectionClassException extends ContainerException implements ContainerExceptionInterface
{
}
