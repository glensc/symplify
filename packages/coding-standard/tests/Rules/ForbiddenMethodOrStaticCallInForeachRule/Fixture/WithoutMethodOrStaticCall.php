<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class WithoutMethodOrStaticCall
{
    public function execute($arg)
    {
        $data = [];
        foreach ($data as $key => $item) {

        }
    }

}