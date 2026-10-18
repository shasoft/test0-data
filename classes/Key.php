<?php

namespace Shasoft\TData;

// Работа с ключами
class Key
{
    // Сгенерировать ключ из списка значений
    static protected function transformValue(mixed $value): mixed
    {
        if (is_array($value)) {
        }
        return $value;
    }
    // Сгенерировать ключ из списка значений
    static public function byValues(array $values): string
    {
        return substr(json_encode(array_map('self::transformValue', $values)), 1, -1);
    }
    // Сгенерировать ключ из значения
    static public function toKey(mixed $value): string
    {
        return serialize(self::transformValue($value));
    }
    // Сгенерировать значения из ключа
    static public function toValue(string $key): mixed
    {
        return unserialize($key);
    }
    // Сгенерировать ключ из значения полей массива
    static public function toArrKey(array $arr, array $fieldnames): string
    {
        // Сгенерировать массив значений
        $keyVal = [];
        foreach ($fieldnames as $fieldname) {
            $keyVal[] = $arr[$fieldname];
        }
        // Сгенерировать ключ
        return self::toKey($keyVal);
    }
}
