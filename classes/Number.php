<?php

namespace Shasoft\TData;

use Deprecated;


#[Deprecated('$v1 <=> $v2')]
class Number
{
    // Сравнить два числа
    static public function compare(int|float $a, int|float $b): int
    {
        return ($a == $b) ? 0 : ($a < $b ? -1 : 1);
    }
}
