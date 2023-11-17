<?php

declare(strict_types=1);

namespace Casbin\Config;

use Casbin\Exceptions\CasbinException;

/**
 * ConfigContract defines the behavior of a Config implementation.
 *
 * @author techlee@qq.com
 */
interface ConfigContract
{
    /**
     * lookups up the value using the provided key and converts the value to a string.
     */
    public function getString(string $key): string;

    /**
     * lookups up the value using the provided key and converts the value to an array of string
     * by splitting the string by comma.
     */
    public function getStrings(string $key): array;

    /**
     * sets the value for the specific key in the Config.
     *
     * @throws CasbinException
     */
    public function set(string $key, string $value): void;

    /**
     * section.key or key.
     */
    public function get(string $key): string;
}
