<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class SpreadOperator
{
    public function __construct(array $args)
    {
        echo sprintf('%s', ...$args);
    }
}
