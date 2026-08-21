<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\UnionTypes;

final class MockUnionProperty
{
    public string|int $limit = 10;
}
