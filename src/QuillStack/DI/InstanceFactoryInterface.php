<?php

declare(strict_types=1);

namespace QuillStack\DI;

interface InstanceFactoryInterface
{
    /**
     * Create the instance of the class named as $id.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function create(string $id);
}
