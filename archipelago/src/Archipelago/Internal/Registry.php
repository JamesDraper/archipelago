<?php

/**
 * @internal
 *
 * @template TValue
 */
class Archipelago_Internal_Registry
{
    /**
     * @var array<string, TValue>
     */
    private $map;

    public function __construct()
    {
        $this->map = array();
    }

    /**
     * @param string $key
     * @param TValue $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->map[$key] = $value;
    }

    /**
     * @param string $key
     * @return TValue
     */
    public function get($key)
    {
        return $this->map[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->map);
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }
}
