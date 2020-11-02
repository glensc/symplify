<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckRequiredInterfaceInContractNamespaceRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckRequiredInterfaceInContractNamespaceRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/Contract/AContractWithInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredInterfaceInContractNamespaceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
