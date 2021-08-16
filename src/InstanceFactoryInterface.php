<?php

namespace Quillstack\DI;

interface InstanceFactoryInterface
{
    /**
     * Create the instance of the class named as $id.
     *
     * @param string $id
     * @codeCoverageIgnore
     *
     * @return object
     */
    public function create(string $id): object;
}
