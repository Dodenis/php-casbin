<?php

declare(strict_types=1);

namespace Casbin\Effector;

/**
 * Class Effector.
 *
 * @author techlee@qq.com
 */
abstract class Effector
{
    public const ALLOW = 0;

    public const INDETERMINATE = 1;

    public const DENY = 2;

    abstract public function mergeEffects(string $expr, array $effects, array $matches, int $policyIndex, int $policyLength): array;
}
