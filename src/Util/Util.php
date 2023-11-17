<?php

declare(strict_types=1);

namespace Casbin\Util;

/**
 * Class Util.
 *
 * @author techlee@qq.com
 */
class Util
{
    public const REGEXP = '/\beval\((?P<rule>[^),]*)\)/';

    /**
     * Escapes the dots in the assertion, because the expression evaluation doesn't support such variable names.
     */
    public static function escapeAssertion(string $s): string
    {
        if (0 === strpos($s, 'r') || 0 === strpos($s, 'p')) {
            $pos = strpos($s, '.');

            if (false !== $pos) {
                $s[$pos] = '_';
            }
        }

        $ss = preg_replace_callback('~(\\|| |=|\\)|\\(|&|<|>|,|\\+|-|!|\\*|\\/)((r|p)[0-9]*)(\\.)~', function ($m) {
            return $m[1] . $m[2] . '_';
        }, $s);

        return is_string($ss) ? $ss : $s;
    }

    /**
     *Removes the comments starting with # in the text.
     */
    public static function removeComments(string $s): string
    {
        $pos = strpos($s, '#');

        return false === $pos ? $s : trim(substr($s, 0, $pos));
    }

    /**
     * Gets a printable string for a string array.
     */
    public static function arrayToString(array $s): string
    {
        return implode(', ', $s);
    }

    /**
     * Removes any duplicated elements in a string array.
     */
    public static function arrayRemoveDuplicates(array &$s): void
    {
        $s = array_keys(array_flip($s));
    }

    /**
     * Determine whether matcher contains function eval.
     */
    public static function hasEval(string $s): bool
    {
        return (bool) preg_match(self::REGEXP, $s);
    }

    /**
     * Replace function eval with the value of its parameters.
     */
    public static function replaceEval(string $s, string $rule): string
    {
        return (string) preg_replace(self::REGEXP, '(' . $rule . ')', $s);
    }

    /**
     * Returns the parameters of function eval.
     */
    public static function getEvalValue(string $s): array
    {
        preg_match_all(self::REGEXP, $s, $matches);

        return $matches['rule'];
    }

    /**
     * ReplaceEvalWithMap replace function eval with the value of its parameters via given sets.
     */
    public static function replaceEvalWithMap(string $src, array $sets): string
    {
        return preg_replace_callback(self::REGEXP, function ($s) use ($sets): string {
            $s = $s[0];
            preg_match(self::REGEXP, $s, $subs);

            if (0 == count($subs)) {
                return $s;
            }
            $key = $subs[1];

            if (isset($sets[$key])) {
                $found = true;
                $value = $sets[$key];
            } else {
                $found = false;
            }

            if (!$found) {
                return $s;
            }

            return preg_replace(self::REGEXP, $s, $value);
        }, $src);
    }
}
