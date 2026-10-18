<?php

namespace Shasoft\TData;

class Str
{
    // Получить формат
    protected static function prepareFormat(string $format): string
    {
        // Преобразовать к нижнему регистру
        $format = strtolower($format);
        if (!in_array($format, ['json', 'php'])) {
            if (in_array($format, ['ser', 'dat'])) {
                $format = 'ser';
            }
        }
        return $format;
    }
    public static function to(mixed $data, string $format = '', ?int $options = null): string
    {
        // Трансформировать данные в зависимости от формата
        switch (self::prepareFormat($format)) {
            case 'json': {
                    if (is_null($options)) {
                        // https://stackoverflow.com/questions/46305169/php-json-encode-malformed-utf-8-characters-possibly-incorrectly-encoded
                        // Malformed UTF-8 characters, possibly incorrectly encoded => JSON_INVALID_UTF8_SUBSTITUTE
                        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE; //JSON_INVALID_UTF8_IGNORE;
                    }
                    /*
                // Symfony\Polyfill\Intl\Normalizer\Normalizer
                foreach ($data as $classname => $classInfo) {
                echo $classname . "\t" . $classInfo['filename'] . "\n";
                $data = json_encode($classInfo, $options);
                if ($data === false) {
                exit(1);
                }
                }
                //*/
                    $ret = json_encode($data, $options);
                    if ($ret === false) {
                        echo "json_encode()\n\t" . json_last_error_msg() . "\n";
                        exit(1);
                    }
                }
                break;
            case 'php': {
                    $phpMarker = '<?php';
                    if (substr($data, 0, strlen($phpMarker)) != $phpMarker) {
                        $ret = $phpMarker . "\n" . $data;
                    } else {
                        $ret = $data;
                    }
                }
                break;
            case 'ser': {
                    $ret = \serialize($data);
                }
                break;
            default: {
                    $ret = $data;
                }
                break;
        }
        return $ret;
    }

    // Строку в данные
    public static function from(string $str, string $format = ''): mixed
    {
        // Трансформировать данные в зависимости от формата
        switch (self::prepareFormat($format)) {
            case 'json': {
                    $ret = json_decode($str, true, 512);
                    if (json_last_error() != JSON_ERROR_NONE) {
                        throw new \Exception('Parse json data');
                    }
                }
                break;
            case 'php': {
                    throw new \Exception('Нет реализации!');
                }
                break;
            case 'ser': {
                    $ret = \unserialize($str);
                }
                break;
            default: {
                    $ret = $str;
                }
                break;
        }
        return $ret;
    }
    // Преобразовать строку в значение
    static function toValue(string $value): int|float|bool|null
    {
        // Преобразовать значение
        switch (strtolower($value)) {
            case 'null':
                $value = null;
                break;
            case 'true':
            case 'on':
            case 'yes':
            case '+':
                $value = true;
                break;
            case 'false':
            case 'off':
            case 'no':
            case '-':
                $value = false;
                break;
            default: {
                    if (filter_var($value, FILTER_VALIDATE_INT)) {
                        $value = intval($value);
                    } else
                        if (filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $value = floatval($value);
                    }
                }
                break;
        }
        return $value;
    }

    // Сгенерировать случайную строку заданной длинны
    static public function random(int $length, ?string $symbols = null): string
    {
        if (empty($symbols)) {
            $symbols = 'A--Z';
        }
        while (true) {
            $pos = mb_strpos($symbols, '--');
            if ($pos == false) {
                break;
            }
            $chStart = mb_substr($symbols, $pos - 1, 1);
            $chEnd = mb_substr($symbols, $pos + 2, 1);
            $symbols = mb_substr($symbols, 0, $pos - 1) . implode('', range($chStart, $chEnd)) . mb_substr($symbols, $pos + 3);
        }
        $chars = str_split($symbols);
        $cnt = count($chars) - 1;
        $ret = '';
        for ($i = 0; $i < $length; $i++) {
            $ret .= $chars[random_int(0, $cnt)];
        }
        return $ret;
    }

    static public function removePrefix(string $value, ?string $prefix = null): string
    {
        if (!empty($prefix)) {
            if (str_starts_with($value, $prefix)) {
                $value = substr($value, strlen($prefix));
            }
        }
        return $value;
    }

    static public function removeSuffix(string $value, ?string $suffix = null): string
    {
        if (!empty($suffix)) {
            if (str_ends_with($value, $suffix)) {
                $value = substr($value, 0, -strlen($suffix));
            }
        }
        return $value;
    }

    static public function getClass(string $classname): string
    {
        $tmp = explode('\\', $classname);
        return array_pop($tmp);
    }
}
