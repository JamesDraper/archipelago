<?php

/**
 * @internal
 */
class Archipelago_Internal_Registry_UnactivatedIslandRegistry extends Archipelago_Internal_Registry_Registry
{
    /**
     * @var self|null
     */
    private static $instance = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public function add(Archipelago_Internal_Island $island)
    {
        parent::add($island);

        $island->getBootstrapper()->onRegister();
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        if (array_key_exists($name, $this->map)) {
            unset($this->map[$name]);
        }
    }
}
