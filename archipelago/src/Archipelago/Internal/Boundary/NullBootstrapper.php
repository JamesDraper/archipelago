<?php

/**
 * @internal
 */
class Archipelago_Internal_Boundary_NullBootstrapper implements Archipelago_Island_Boundary_Bootstrapper
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

    public function onFirstRun()
    {
        // Do nothing.
    }

    public function onRegister()
    {
        // Do nothing.
    }
}
