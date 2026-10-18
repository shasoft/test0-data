<?php

namespace Shasoft\TData;

class Base64
{
    public static function encode(string $data, bool $asUrl = false): string
    {
        $ret = base64_encode($data);
        if ($asUrl) {
            $ret =  rtrim(strtr($ret, '+/', '-_'), '=');
        }
        return $ret;
    }

    public static function decode(string $data, bool $asUrl = false): string
    {
        if ($asUrl) {
            $data = str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT);
        }
        return base64_decode($data);
    }
}
