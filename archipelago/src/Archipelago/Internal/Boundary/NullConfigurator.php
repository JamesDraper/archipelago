<?php

/**
 * @internal
 */
class Archipelago_Internal_Boundary_NullConfigurator implements Archipelago_Island_Boundary_Configurator
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

    public function configure(Archipelago_Island_Inner_Ingress $ingress)
    {
        // Do nothing.
    }
}
