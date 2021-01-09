<?php

namespace QuillStack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

final class ClassNotFoundForInterfaceException extends ContainerException implements NotFoundExceptionInterface
{
}
