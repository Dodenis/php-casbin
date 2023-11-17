<?php

namespace Casbin\Tests\Watcher;

use Casbin\Enforcer;
use PHPUnit\Framework\TestCase;

/**
 * UtilTest.
 *
 * @author techlee@qq.com
 *
 * @internal
 */
class WatcherUpdatableTest extends TestCase
{
    protected $enforcer;
    protected $watcher;
    protected $isCalled;

    public function initWatcher(): void
    {
        $this->isCalled = false;
        $this->watcher = new SampleWatcherUpdatable();
        $this->enforcer = new Enforcer('examples/rbac_model.conf', 'examples/rbac_policy.csv');
        $this->enforcer->setWatcher($this->watcher);
    }

    public function testUpdateForUpdatePolicy(): void
    {
        $this->initWatcher();
        $this->watcher->setUpdateCallback(function (): void {
            $this->isCalled = true;
        });
        $this->watcher->updateForUpdatePolicy([], []);
        $this->assertTrue($this->isCalled);
    }

    public function testUpdateForUpdatePolicies(): void
    {
        $this->initWatcher();
        $this->watcher->setUpdateCallback(function (): void {
            $this->isCalled = true;
        });
        $this->watcher->updateForUpdatePolicies([], []);
        $this->assertTrue($this->isCalled);
    }
}
