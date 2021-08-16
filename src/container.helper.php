<?php

declare(strict_types=1);

use Quillstack\DI\Container;

if (!function_exists('container')) {
    /**
     * @return Container
     */
    function container(): Container
    {
        return Container::getInstance();
    }
}
