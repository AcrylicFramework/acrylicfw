<?php

namespace App\Includes;

/**
 * Container for parameter
 */
class Param {
    protected array $params;
    public function __construct(array $params = []) {
        $this->params = $params;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function keys(): array
    {
        return array_keys($this->params);
    }

    public function count(): int
    {
        return \count($this->params);
    }

    public function exists(string $key): bool
    {
        return \array_key_exists($key, $this->params);
    }

    public function add(array $params = []): void
    {
        $this->params = \array_replace($this->params, $params);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return \array_key_exists($key, $this->params) ? $this->params[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($this->params[$key]);
    }

    public function toString(): string {
        $str = '';
        foreach ($this->all() as $name => $value) {
            $str .= $name . ': ' . $value . "\n";
        }
        return $str;
    }
}