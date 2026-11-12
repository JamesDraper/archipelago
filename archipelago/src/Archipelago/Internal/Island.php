<?php

/**
 * @internal
 */
class Archipelago_Internal_Island
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Archipelago_Island_Inner_Ingress
     */
    private $ingress;

    /**
     * @var Archipelago_Island_Boundary_Bootstrapper
     */
    private $bootstrapper;

    /**
     * @var Archipelago_Island_Boundary_Configurator
     */
    private $configurator;

    /**
     * @var Archipelago_Island_Boundary_Adapter
     */
    private $adapter;

    /**
     * @param string $name
     * @param Archipelago_Island_Inner_Ingress $ingress
     * @param Archipelago_Island_Boundary_Bootstrapper $bootstrapper
     * @param Archipelago_Island_Boundary_Configurator $configurator
     * @param Archipelago_Island_Boundary_Adapter $adapter
     */
    public function __construct(
        $name,
        Archipelago_Island_Inner_Ingress $ingress,
        Archipelago_Island_Boundary_Bootstrapper $bootstrapper,
        Archipelago_Island_Boundary_Configurator $configurator,
        Archipelago_Island_Boundary_Adapter $adapter
    ) {
        $this->name = $name;
        $this->ingress = $ingress;
        $this->bootstrapper = $bootstrapper;
        $this->configurator = $configurator;
        $this->adapter = $adapter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Archipelago_Island_Inner_Ingress
     */
    public function getIngress()
    {
        return $this->ingress;
    }

    /**
     * @return Archipelago_Island_Boundary_Bootstrapper
     */
    public function getBootstrapper()
    {
        return $this->bootstrapper;
    }

    /**
     * @return Archipelago_Island_Boundary_Configurator
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @return Archipelago_Island_Boundary_Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
