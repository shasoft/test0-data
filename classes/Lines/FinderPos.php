<?php

namespace Shasoft\TData\Lines;

use Shasoft\TData\Lines\Finder;

class FinderPos extends Finder
{
    public function __construct(protected string $needle) {}

    public function compare(string $line): bool
    {
        return strpos($line, $this->needle) !== false;
    }
}
