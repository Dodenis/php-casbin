<?php

declare(strict_types=1);

namespace Casbin\Persist;

use Casbin\Model\Model;

/**
 * Adapter is the interface for Casbin adapters.
 *
 * @author techlee@qq.com
 */
interface Adapter
{
    /**
     * Loads all policy rules from the storage.
     */
    public function loadPolicy(Model $model): void;

    /**
     * Saves all policy rules to the storage.
     */
    public function savePolicy(Model $model): void;

    /**
     * Adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void;

    /**
     * This is part of the Auto-Save feature.
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void;

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void;
}
