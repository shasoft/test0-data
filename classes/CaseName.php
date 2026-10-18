<?php
// https://ru.wikipedia.org/wiki/Snake_case 
// https://ru.wikipedia.org/wiki/CamelCase
namespace Shasoft\TData;

// Преобразование регистра букв
class CaseName
{
    public static function snakeToCamel(string $name, string $delimiter = '_'): string
    {
        return lcfirst(implode('', array_map(function (string $namePart) {
            return ucfirst($namePart);
        }, explode($delimiter, $name))));
    }
    public static function camelToSnake(string $name, string $delimiter = '_'): string
    {
        // insert hyphen between any letter and the beginning of a numeric chain
        $ret = preg_replace('/([a-z]+)([0-9]+)/i', '$1' . $delimiter . '$2', $name);
        // insert hyphen between any lower-to-upper-case letter chain
        $ret = preg_replace('/([a-z]+)([A-Z]+)/', '$1' . $delimiter . '$2', $ret);
        // insert hyphen between the end of a numeric chain and the beginning of an alpha chain
        $ret = preg_replace('/([0-9]+)([a-z]+)/i', '$1' . $delimiter . '$2', $ret);

        // Lowercase
        $ret = strtolower($ret);

        return $ret;
    }
}
