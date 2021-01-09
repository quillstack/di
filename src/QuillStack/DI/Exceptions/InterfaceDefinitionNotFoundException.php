<?php

namespace QuillStack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

final class InterfaceDefinitionNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
