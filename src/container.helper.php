<?php

use QuillStack\DI\Container;

if (!function_exists('container')) {
    /**
     * @return Container
     */
    function container(): Container
    {
        return Container::getInstance();
    }
}
