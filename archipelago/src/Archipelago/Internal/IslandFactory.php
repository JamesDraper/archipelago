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
     * @var Archipelago_Internal_Boundary_NullBootstrapper
     */
    private $nullBootstrapper;

    /**
     * @var Archipelago_Internal_Boundary_NullConfigurator
     */
    private $nullConfigurator;

    /**
     * @var Archipelago_Internal_Boundary_NullAdapter
     */
    private $nullAdapter;

    public function __construct(
        Archipelago_Internal_Validator_Validator $validator,
        Archipelago_Internal_Boundary_NullBootstrapper $nullBootstrapper,
        Archipelago_Internal_Boundary_NullConfigurator $nullConfigurator,
        Archipelago_Internal_Boundary_NullAdapter $nullAdapter,
    ) {
        $this->validator = $validator;
        $this->nullBootstrapper = $nullBootstrapper;
        $this->nullConfigurator = $nullConfigurator;
        $this->nullAdapter = $nullAdapter;
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
            ? $this->nullBootstrapper
            : $bootstrapper;

        $configurator = (null === $configurator)
            ? $this->nullConfigurator
            : $configurator;

        $adapter = (null === $adapter)
            ? $this->nullAdapter
            : $adapter;

        return new Archipelago_Internal_Island($name, $ingress, $bootstrapper, $configurator, $adapter);
    }
}
