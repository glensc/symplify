<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Rules\AbstractSymplifyRule;

/**
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongClassLikeRule\TooLongClassLikeRuleTest
 */
final class TooLongClassLikeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s has %d lines, it is too long. Shorted it under %d lines';

    /**
     * @var int
     */
    private $maxClassLikeLength;

    public function __construct(int $maxClassLikeLength)
    {
        $this->maxClassLikeLength = $maxClassLikeLength;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentClassLenght = $this->getNodeLength($node);
        if ($currentClassLenght <= $this->maxClassLikeLength) {
            return [];
        }

        $classLikeType = $this->resolveClassLikeType($node);

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classLikeType, $currentClassLenght, $this->maxClassLikeLength);
        return [$errorMessage];
    }

    private function resolveClassLikeType(ClassLike $classLike): string
    {
        if ($classLike instanceof Class_) {
            return 'Class';
        }

        if ($classLike instanceof Interface_) {
            return 'Interface';
        }

        return 'Trait';
    }

    private function getNodeLength(Node $node): int
    {
        return $node->getEndLine() - $node->getStartLine();
    }
}
