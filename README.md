Пакет для манипуляции данными

```php
// Данные в строку
$str = Str::to([1, true, 2.3, 'Строка']);
// Строку в данные
$data = Str::from($str);
var_dump($data);
// Вывод
array(4) {
  [0] => int(1)
  [1] => bool(true)
  [2] => float(2.3)
  [3] => string(12) "Строка"
}
```