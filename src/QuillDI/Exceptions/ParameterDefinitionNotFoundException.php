<?php

declare(strict_types=1);

namespace QuillDI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ParameterDefinitionNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    //
}
