<?php

namespace Shasoft\TData;

use ArrayAccess;

// Работа с массивом
class Arr
{
    // Установить значение элементов по умолчанию
    public static function setDefault(array &$arr, array $values): void
    {
        foreach ($values as $key => $value) {
            if (!array_key_exists($key, $arr)) {
                $arr[$key] = $value;
            }
        }
    }
    // Сравнить два массива
    private static function _equals(array $arr1, array $arr2): bool
    {
        // Check if the number of elements in the arrays is different
        if (count($arr1) != count($arr2)) {
            return false;
        }

        // Sort the arrays to ensure the elements are in the same order
        sort($arr1);
        sort($arr2);

        // Loop through each element
        foreach ($arr1 as $key => $value) {
            // If the element is an array, recursively call the compareArrays function
            if (is_array($value) && is_array($arr2[$key])) {
                if (!self::_equals($value, $arr2[$key])) {
                    return false;
                }
            } else {
                // If the element is not an array, compare them directly
                if ($value !== $arr2[$key]) {
                    return false;
                }
            }
        }

        return true; // Return true if all elements match        
    }
    // Сравнить два массива
    public static function equals(array $arr1, array $arr2): bool
    {
        return self::_equals($arr1, $arr2) && self::_equals($arr2, $arr1);
    }

    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    public static function exists(ArrayAccess|array $array, string|int $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    public static function get(ArrayAccess|array $array, string|int|null $key, mixed $default = null): mixed
    {
        if (! static::accessible($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    static function map_recursive(callable $callback, array $array): array
    {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : $callback($item);
        };

        return array_map($func, $array);
    }
}
