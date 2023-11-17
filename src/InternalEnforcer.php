<?php

declare(strict_types=1);

namespace Casbin;

use Casbin\Exceptions\NotImplementedException;
use Casbin\Log\Log;
use Casbin\Model\Policy;
use Casbin\Persist\BatchAdapter;
use Casbin\Persist\UpdatableAdapter;
use Casbin\Persist\WatcherEx;
use Casbin\Persist\WatcherUpdatable;

/**
 * InternalEnforcer = CoreEnforcer + Internal API.
 *
 * @author techlee@qq.com
 */
class InternalEnforcer extends CoreEnforcer
{
    protected function shouldPersist(): bool
    {
        return !is_null($this->adapter) && $this->autoSave;
    }

    /**
     * Adds a rule to the current policy.
     */
    protected function addPolicyInternal(string $sec, string $ptype, array $rule): bool
    {
        if ($this->model->hasPolicy($sec, $ptype, $rule)) {
            return false;
        }

        if ($this->shouldPersist()) {
            try {
                $this->adapter->addPolicy($sec, $ptype, $rule);
            } catch (NotImplementedException $e) {
            }
        }

        $this->model->addPolicy($sec, $ptype, $rule);

        if ('g' == $sec) {
            $this->buildIncrementalRoleLinks(Policy::POLICY_ADD, $ptype, [$rule]);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            if ($this->watcher instanceof WatcherEx) {
                $this->watcher->updateForAddPolicy($sec, $ptype, ...$rule);
            } else {
                $this->watcher->update();
            }
        }

        return true;
    }

    /**
     * Adds rules to the current policy.
     *
     * @throws Exceptions\CasbinException
     */
    protected function addPoliciesInternal(string $sec, string $ptype, array $rules): bool
    {
        if ($this->model->hasPolicies($sec, $ptype, $rules)) {
            return false;
        }

        if ($this->shouldPersist() && $this->adapter instanceof BatchAdapter) {
            try {
                $this->adapter->addPolicies($sec, $ptype, $rules);
            } catch (NotImplementedException $e) {
            }
        }

        $this->model->addPolicies($sec, $ptype, $rules);

        if ('g' == $sec) {
            $this->buildIncrementalRoleLinks(Policy::POLICY_ADD, $ptype, $rules);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            $this->watcher->update();
        }

        return true;
    }

    /**
     * @param string[] $oldRule
     * @param string[] $newRule
     */
    protected function updatePolicyInternal(string $sec, string $ptype, array $oldRule, array $newRule): bool
    {
        if ($this->shouldPersist() && $this->adapter instanceof UpdatableAdapter) {
            try {
                $this->adapter->updatePolicy($sec, $ptype, $oldRule, $newRule);
            } catch (NotImplementedException $e) {
            }
        }

        $ruleUpdated = $this->model->updatePolicy($sec, $ptype, $oldRule, $newRule);

        if (!$ruleUpdated) {
            return false;
        }

        if ('g' == $sec) {
            // remove the old rule
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, [$oldRule]);

            // add the new rule
            $this->buildIncrementalRoleLinks(Policy::POLICY_ADD, $ptype, [$newRule]);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            try {
                if ($this->watcher instanceof WatcherUpdatable) {
                    $this->watcher->updateForUpdatePolicy($oldRule, $newRule);
                } else {
                    $this->watcher->update();
                }
            } catch (\Exception $e) {
                Log::logPrint('An exception occurred:' . $e->getMessage());

                return false;
            }
        }

        return true;
    }

    protected function updatePoliciesInternal(string $sec, string $ptype, array $oldRules, array $newRules): bool
    {
        if ($this->shouldPersist() && $this->adapter instanceof UpdatableAdapter) {
            try {
                $this->adapter->updatePolicies($sec, $ptype, $oldRules, $newRules);
            } catch (NotImplementedException $e) {
            }
        }

        $ruleUpdated = $this->model->updatePolicies($sec, $ptype, $oldRules, $newRules);

        if (!$ruleUpdated) {
            return false;
        }

        if ('g' == $sec) {
            // remove the old rule
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, $oldRules);

            // add the new rule
            $this->buildIncrementalRoleLinks(Policy::POLICY_ADD, $ptype, $newRules);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            try {
                if ($this->watcher instanceof WatcherUpdatable) {
                    $this->watcher->updateForUpdatePolicies($oldRules, $newRules);
                } else {
                    $this->watcher->update();
                }
            } catch (\Exception $e) {
                Log::logPrint('An exception occurred:' . $e->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * Removes a rule from the current policy.
     */
    protected function removePolicyInternal(string $sec, string $ptype, array $rule): bool
    {
        if ($this->shouldPersist()) {
            try {
                $this->adapter->removePolicy($sec, $ptype, $rule);
            } catch (NotImplementedException $e) {
            }
        }

        $ruleRemoved = $this->model->removePolicy($sec, $ptype, $rule);

        if (!$ruleRemoved) {
            return false;
        }

        if ('g' == $sec) {
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, [$rule]);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            if ($this->watcher instanceof WatcherEx) {
                $this->watcher->updateForRemovePolicy($sec, $ptype, ...$rule);
            } else {
                $this->watcher->update();
            }
        }

        return true;
    }

    /**
     * Removes a rules from the current policy.
     */
    protected function removePoliciesInternal(string $sec, string $ptype, array $rules): bool
    {
        if (!$this->model->hasPolicies($sec, $ptype, $rules)) {
            return false;
        }

        if ($this->shouldPersist() && $this->adapter instanceof BatchAdapter) {
            try {
                $this->adapter->removePolicies($sec, $ptype, $rules);
            } catch (NotImplementedException $e) {
            }
        }

        $ruleRemoved = $this->model->removePolicies($sec, $ptype, $rules);

        if (!$ruleRemoved) {
            return false;
        }

        if ('g' == $sec) {
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, $rules);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            // error intentionally ignored
            $this->watcher->update();
        }

        return true;
    }

    /**
     * Removes rules based on field filters from the current policy.
     */
    protected function removeFilteredPolicyInternal(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): bool
    {
        if ($this->shouldPersist()) {
            try {
                $this->adapter->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
            } catch (NotImplementedException $e) {
            }
        }

        $ruleRemoved = $this->model->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);

        if (!$ruleRemoved) {
            return false;
        }

        if ('g' == $sec) {
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, $ruleRemoved);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            // error intentionally ignored
            if ($this->watcher instanceof WatcherEx) {
                $this->watcher->updateForRemoveFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
            } else {
                $this->watcher->update();
            }
        }

        return true;
    }

    protected function updateFilteredPoliciesInternal(string $sec, string $ptype, array $newRules, int $fieldIndex, string ...$fieldValues): bool
    {
        $oldRules = [];

        if ($this->shouldPersist()) {
            try {
                if ($this->adapter instanceof UpdatableAdapter) {
                    $oldRules = $this->adapter->updateFilteredPolicies($sec, $ptype, $newRules, $fieldIndex, ...$fieldValues);
                }
            } catch (NotImplementedException $e) {
            }
        }

        $ruleChanged = $this->model->removePolicies($sec, $ptype, $oldRules);
        $this->model->addPolicies($sec, $ptype, $newRules);

        $ruleChanged = $ruleChanged && 0 !== count($newRules);

        if (!$ruleChanged) {
            return $ruleChanged;
        }

        if ('g' == $sec) {
            // remove the old rules
            $this->buildIncrementalRoleLinks(Policy::POLICY_REMOVE, $ptype, $oldRules);
            // add the new rules
            $this->buildIncrementalRoleLinks(Policy::POLICY_ADD, $ptype, $newRules);
        }

        if (null !== $this->watcher && $this->autoNotifyWatcher) {
            // error intentionally ignored
            if ($this->watcher instanceof WatcherUpdatable) {
                $this->watcher->updateForUpdatePolicies($oldRules, $newRules);
            } else {
                $this->watcher->update();
            }

            return $ruleChanged;
        }

        return $ruleChanged;
    }

    /**
     * Undocumented function.
     */
    protected function getDomainIndex(string $ptype): int
    {
        $p = $this->model['p'][$ptype];
        $pattern = sprintf('%s_dom', $ptype);
        $index = count($p->tokens);

        $tempIndex = array_search($pattern, $p->tokens);

        if (false !== $tempIndex) {
            $index = intval($tempIndex);
        }

        return $index;
    }
}
