<?php

namespace Shasoft\TData\Lines;

use Shasoft\TData\Lines\Finder;

class Lines
{
    const EOL = "\n";
    protected array $lines;

    public function __construct(string|array|null|self $text = null)
    {
        if (is_null($text)) {
            $this->lines = [];
        } else if ($text instanceof Lines) {
            $this->lines = $text->lines;
        } else if (is_string($text)) {
            $this->lines = self::split($text);
        } else if (is_array($text)) {
            $this->lines = $text;
        }
    }

    static public function load(string $filepath): static
    {
        return new static(file_get_contents($filepath));
    }

    static public function split(string $code): array
    {
        return explode(self::EOL, $code);
    }

    public function __toString()
    {
        return implode(self::EOL, $this->lines);
    }

    public function lines(): array
    {
        return $this->lines;
    }

    public function slice(int $offset = 0, ?int $length = null): array
    {
        return array_slice($this->lines, $offset, $length);
    }

    public function splice(int $offset, ?int $length = null, mixed $replacement = []): static
    {
        array_splice($this->lines, $offset, $length, $replacement);
        return $this;
    }

    public function count(): int
    {
        return count($this->lines);
    }

    public function trim_hr(?string $characters = null): static
    {
        if (is_null($characters)) {
            $this->lines = array_map('rtrim', $this->lines);
        } else {
            $this->lines = array_map(
                function (string $line) use ($characters) {
                    return rtrim($line, $characters);
                },
                $this->lines
            );
        }
        return $this;
    }

    public function trim_hl(?string $characters = null): static
    {
        $minOffset = PHP_INT_MAX;
        foreach ($this->lines as $line) {
            if (is_null($characters)) {
                $ltrim = ltrim($line);
            } else {
                $ltrim = ltrim($line, $characters);
            }
            $lLen = strlen($ltrim);
            if ($lLen > 0) {
                $minOffset = min(
                    strlen($line) - $lLen,
                    $minOffset
                );
            }
        }
        $this->lines = array_map(
            function (string $line) use ($minOffset) {
                return substr($line, $minOffset);
            },
            $this->lines
        );
        return $this;
    }

    public function trim_h(): static
    {
        return $this->trim_hl()->trim_hr();
    }

    public function trim_vt(): static
    {
        while (!empty($this->lines) && empty(trim($this->lines[0]))) {
            $this->lines = array_slice($this->lines, 1);
        }
        return $this;
    }

    public function trim_vb(): static
    {
        while (!empty($this->lines)) {
            $line = array_pop($this->lines);
            if (!empty(trim($line))) {
                $this->lines[] = $line;
                break;
            }
        }
        return $this;
    }

    public function trim_v(): static
    {
        return $this->trim_vt()->trim_vb();
    }

    public function trim(): static
    {
        return $this->trim_h()->trim_v();
    }

    public function insert(string $prefix = ' ', string $suffix = ''): static
    {
        $this->lines = array_map(function (string $line) use ($prefix, $suffix) {
            return $prefix . $line . $suffix;
        }, $this->lines);
        return $this;
    }

    public function findRow(string|Finder|callable $cb, int $offset = 0): int|false
    {
        foreach ($this->lines as $index => $line) {
            if ($index >= $offset) {
                if (is_callable($cb)) {
                    if ($cb($line)) {
                        return $index;
                    }
                } else if (is_object($cb)) {
                    if ($cb instanceof Finder) {
                        if ($cb->compare($line)) {
                            return $index;
                        }
                    }
                } else {
                    if ($cb == $line) {
                        return $index;
                    }
                }
            }
        }
        return false;
    }

    public function map(callable $cb): static
    {
        $this->lines = array_map($cb, $this->lines);
        return $this;
    }

    public function filter(callable $cb): static
    {
        $linesPrev = [];
        $linesNext = $this->lines;
        foreach ($this->lines as $line) {
            $line = array_shift($linesNext);
            if (call_user_func($cb, $line, $linesPrev, $linesNext)) {
                $linesPrev[] = $line;
            }
        }
        $this->lines = $linesPrev;
        return $this;
    }

    public function replace(string|array $search, string|array $replace): static
    {
        $content = str_replace(
            $search,
            $replace,
            (string)$this
        );
        $this->lines = self::split($content);
        return $this;
    }
}
