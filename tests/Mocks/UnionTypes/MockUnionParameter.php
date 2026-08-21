<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\UnionTypes;

use Quillstack\DI\Tests\Mocks\Simple\MockRepository;

/**
 * A union type names no single class to build, so it is looked up in the configuration.
 */
final class MockUnionParameter
{
    public function __construct(
        public readonly MockRepository $repository,
        public readonly string|int $limit = 10
    ) {
        //
    }
}
