<?php

declare(strict_types=1);

namespace Quillstack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class InterfaceDefinitionNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    //
}
