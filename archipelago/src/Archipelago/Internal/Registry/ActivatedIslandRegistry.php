<?php

/**
 * @internal
 */
class Archipelago_Internal_Registry_ActivatedIslandRegistry extends Archipelago_Internal_Registry_Registry
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

        $island->getBootstrapper()->onFirstRun();

        $configurator = $island->getConfigurator();
        $ingress = $island->getIngress();

        $configurator->configure($ingress);
    }
}
