<?php

/**
 * @internal
 */
abstract class Archipelago_Internal_Registry_Registry
{
    /**
     * @var array<string, Archipelago_Internal_Island>
     */
    protected $map;

    public function __construct()
    {
        $this->map = array();
    }

    /**
     * @return void
     */
    public function add(Archipelago_Internal_Island $island)
    {
        $this->map[$island->getName()] = $island;
    }

    /**
     * @param string $name
     * @return Archipelago_Internal_Island|null
     */
    public function get($name)
    {
        return array_key_exists($name, $this->map)
            ? $this->map[$name]
            : null;
    }
}
