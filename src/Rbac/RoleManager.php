<?php

declare(strict_types=1);

namespace Casbin\Rbac;

/**
 * Interface RoleManager.
 * Provides interface to define the operations for managing roles.
 *
 * @author techlee@qq.com
 */
interface RoleManager
{
    /**
     * Clears all stored data and resets the role manager to the initial state.
     */
    public function clear(): void;

    /**
     * Adds the inheritance link between role: name1 and role: name2.
     * aka role: name1 inherits role: name2.
     * domain is a prefix to the roles.
     */
    public function addLink(string $name1, string $name2, string ...$domain): void;

    /**
     * Deletes the inheritance link between role: name1 and role: name2.
     * aka role: name1 does not inherit role: name2 any more.
     * domain is a prefix to the roles.
     */
    public function deleteLink(string $name1, string $name2, string ...$domain): void;

    /**
     * Determines whether role: name1 inherits role: name2.
     * domain is a prefix to the roles.
     */
    public function hasLink(string $name1, string $name2, string ...$domain): bool;

    /**
     * Gets the roles that a subject inherits.
     * domain is a prefix to the roles.
     *
     * @return string[]
     */
    public function getRoles(string $name, string ...$domain): array;

    /**
     * Gets the users that inherits a subject.
     * domain is an unreferenced parameter here, may be used in other implementations.
     *
     * @return string[]
     */
    public function getUsers(string $name, string ...$domain): array;

    /**
     * Prints all the roles to log.
     */
    public function printRoles(): void;

    /**
     * Support use pattern in g.
     */
    public function addMatchingFunc(string $name, \Closure $fn): void;

    /**
     * Support use domain pattern in g.
     */
    public function addDomainMatchingFunc(string $name, \Closure $fn): void;
}
