<?php

declare(strict_types=1);

namespace Casbin\Log;

/**
 * Interface Logger.
 *
 * @author techlee@qq.com
 */
interface Logger
{
    /**
     * Controls whether print the message.
     */
    public function enableLog(bool $enable): void;

    /**
     * Returns if logger is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Formats using the default formats for its operands and logs the message.
     *
     * @param mixed ...$v
     */
    public function write(...$v): void;

    /**
     * Formats according to a format specifier and logs the message.
     *
     * @param mixed ...$v
     */
    public function writef(string $format, ...$v): void;
}
