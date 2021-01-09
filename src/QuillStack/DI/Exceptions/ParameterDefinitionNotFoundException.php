<?php

namespace QuillStack\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ParameterDefinitionNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
