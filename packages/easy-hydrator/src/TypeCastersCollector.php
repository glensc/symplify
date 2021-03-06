<?php declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;
use Symplify\EasyHydrator\TypeCaster\TypeCasterInterface;

final class TypeCastersCollector
{
    /**
     * @var TypeCasterInterface[]
     */
    private $typeCasters = [];

    /**
     * @param TypeCasterInterface[] $typeCasters
     */
    public function __construct(array $typeCasters)
    {
        $this->typeCasters = $this->sortCastersByPriority($typeCasters);
    }

    /**
     * @return mixed
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        foreach ($this->typeCasters as $typeCaster) {
            if ($typeCaster->isSupported($reflectionParameter)) {
                return $typeCaster->retype($value, $reflectionParameter, $classConstructorValuesResolver);
            }
        }

        return $value;
    }

    /**
     * @param TypeCasterInterface[] $typeCasters
     * @return TypeCasterInterface[]
     */
    private function sortCastersByPriority(array $typeCasters): array
    {
        usort($typeCasters, static function (TypeCasterInterface $a, TypeCasterInterface $b): int {
            return $a->getPriority() <=> $b->getPriority();
        });

        return $typeCasters;
    }
}
