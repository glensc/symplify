<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source;

abstract class AnotherParentClassWithParams
{
    protected function process($one, $two): void
    {
    }
}
