<?php

/**
 * @internal
 */
final class Archipelago_Internal_TypeFormatter
{
    /**
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        if (null === $value) {
            return 'null';
        }

        if (is_bool($value)) {
            return 'bool';
        }

        if (is_int($value)) {
            return 'int';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_string($value)) {
            return 'string';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (self::isObject($value)) {
            /** @var object $value */
            return self::formatObject($value);
        }

        // Open and closed resources. gettype() reports a closed resource as
        // "resource (closed)" from PHP 7.2 and "unknown type" before that.
        return 'resource';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private static function isObject($value)
    {
        // Before PHP 7.2, is_object() returns false for __PHP_Incomplete_Class.
        // But gettype() still reports it as an object.
        return is_object($value) || 'object' === gettype($value);
    }

    /**
     * @param object $value
     * @return string
     */
    private static function formatObject($value)
    {
        $class = get_class($value);

        // Closure is a final class (PHP 5.3+), so comparing the class name is
        // safe and avoids instanceof on a class PHP 5.0 does not have.
        if ('Closure' === $class) {
            return 'closure';
        }

        if (self::isEnum($class)) {
            return sprintf('enum(%s)', $class);
        }

        return sprintf('object(%s)', $class);
    }

    /**
     * @param string $class
     * @return bool
     */
    private static function isEnum($class)
    {
        // Enums only exist from PHP 8.1.
        return function_exists('enum_exists') && enum_exists($class, false);
    }
}
