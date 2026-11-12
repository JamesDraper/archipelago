<?php

/**
 * @internal
 */
class Archipelago_Internal_IslandFactory
{
    /**
     * @var Archipelago_Internal_Validator_Validator
     */
    private $validator;

    /**
     * @var Archipelago_Internal_Boundary_NullBootstrapper|null
     */
    private $nullBootstrapper;

    /**
     * @var Archipelago_Internal_Boundary_NullConfigurator|null
     */
    private $nullConfigurator;

    /**
     * @var Archipelago_Internal_Boundary_NullAdapter|null
     */
    private $nullAdapter;

    public function __construct(Archipelago_Internal_Validator_Validator $validator)
    {
        $this->validator = $validator;

        $this->nullBootstrapper = null;
        $this->nullConfigurator = null;
        $this->nullAdapter = null;
    }

    /**
     * @return Archipelago_Internal_Island
     * @throws Archipelago_BadValueException
     * @throws Archipelago_TypeException
     */
    public function make(Archipelago_Island $island)
    {
        $name = $island->getName();
        $ingress = $island->getIngress();
        $bootstrapper = $island->getBootstrapper();
        $configurator = $island->getConfigurator();
        $adapter = $island->getAdapter();

        $this->validator->validate($island, $name, $ingress, $bootstrapper, $configurator, $adapter);

        $bootstrapper = (null === $bootstrapper)
            ? $this->getNullBootstrapper()
            : $bootstrapper;

        $configurator = (null === $configurator)
            ? $this->getNullConfigurator()
            : $configurator;

        $adapter = (null === $adapter)
            ? $this->getNullAdapter()
            : $adapter;

        return new Archipelago_Internal_Island($name, $ingress, $bootstrapper, $configurator, $adapter);
    }

    /**
     * @return Archipelago_Internal_Boundary_NullBootstrapper
     */
    private function getNullBootstrapper()
    {
        if (null === $this->nullBootstrapper) {
            $this->nullBootstrapper = new Archipelago_Internal_Boundary_NullBootstrapper();
        }

        return $this->nullBootstrapper;
    }

    /**
     * @return Archipelago_Internal_Boundary_NullConfigurator
     */
    private function getNullConfigurator()
    {
        if (null === $this->nullConfigurator) {
            $this->nullConfigurator = new Archipelago_Internal_Boundary_NullConfigurator();
        }

        return $this->nullConfigurator;
    }

    /**
     * @return Archipelago_Internal_Boundary_NullAdapter
     */
    private function getNullAdapter()
    {
        if (null === $this->nullAdapter) {
            $this->nullAdapter = new Archipelago_Internal_Boundary_NullAdapter();
        }

        return $this->nullAdapter;
    }
}
