<?php

namespace Shasoft\TData\Lines;

abstract class Finder
{
    abstract public function compare(string $line): bool;
}
