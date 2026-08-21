<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\NestedConfig;

/**
 * Takes a dependency first and a configured value second, which is what used to send the
 * lookup of `$name` to the class resolved in between.
 */
final class MockOuterService
{
    public function __construct(
        public readonly MockInnerService $inner,
        public readonly string $name = 'outer default'
    ) {
        //
    }
}
