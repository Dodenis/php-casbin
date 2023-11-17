<?php

declare(strict_types=1);

namespace Casbin\Persist;

use Casbin\Model\Model;

/**
 * Interface WatcherEx
 * WatcherEx is the strengthen for Casbin watchers.
 *
 * @author ab1652759879@gmail.com
 */
interface WatcherEx extends Watcher
{
    /**
     * updateForAddPolicy calls the update callback of other instances to synchronize their policy.
     * It is called after addPolicy() method of Enforcer class.
     */
    public function updateForAddPolicy(string $sec, string $ptype, string ...$params): void;

    /**
     * updateForRemovePolicy calls the update callback of other instances to synchronize their policy.
     * It is called after removePolicy() method of Enforcer class.
     */
    public function updateForRemovePolicy(string $sec, string $ptype, string ...$params): void;

    /**
     * updateForRemoveFilteredPolicy calls the update callback of other instances to synchronize their policy.
     * It is called after removeFilteredNamedGroupingPolicy() method of Enforcer class.
     */
    public function updateForRemoveFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void;

    /**
     * updateForSavePolicy calls the update callback of other instances to synchronize their policy.
     * It is called after removeFilteredNamedGroupingPolicy() method of Enforcer class.
     */
    public function updateForSavePolicy(Model $model): void;
}
