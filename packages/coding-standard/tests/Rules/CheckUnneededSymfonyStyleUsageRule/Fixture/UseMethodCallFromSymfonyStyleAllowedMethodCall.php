<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\Fixture;

use Symfony\Component\Console\Style\SymfonyStyle;

class UseMethodCallFromSymfonyStyleAllowedMethodCall
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run()
    {
        $this->symfonyStyle->success('test');
    }
}
