<?php

declare(strict_types=1);

namespace QuillDI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

final class InterfaceDefinitionNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    //
}
