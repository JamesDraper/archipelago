<?php

/**
 * @internal
 */
class Archipelago_Internal_Registry
{
    /**
     * @var array<string, Archipelago_Internal_Island>
     */
    private $map;

    public function __construct()
    {
        $this->map = array();
    }

    /**
     * @param string $key
     * @return void
     */
    public function set($key, Archipelago_Internal_Island $value)
    {
        $this->map[$key] = $value;
    }

    /**
     * @param string $key
     * @return Archipelago_Internal_Island
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
        unset($this->map[$key]);
    }
}
