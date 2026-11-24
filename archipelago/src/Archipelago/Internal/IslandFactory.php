<?php

/**
 * @internal
 */
class Archipelago_Internal_IslandFactory
{
    /**
     * @var self|null
     */
    private static $instance = null;

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

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            require_once dirname(__FILE__) . '/Validator/Validator.php';
            require_once dirname(__FILE__) . '/Boundary/NullBootstrapper.php';
            require_once dirname(__FILE__) . '/Boundary/NullConfigurator.php';
            require_once dirname(__FILE__) . '/Boundary/NullAdapter.php';

            $validator = Archipelago_Internal_Validator_Validator::getInstance();
            $nullBootstrapper = Archipelago_Internal_Boundary_NullBootstrapper::getInstance();
            $nullConfigurator = Archipelago_Internal_Boundary_NullConfigurator::getInstance();
            $nullAdapter = Archipelago_Internal_Boundary_NullAdapter::getInstance();

            self::$instance = new self($validator, $nullBootstrapper, $nullConfigurator, $nullAdapter);
        }

        return self::$instance;
    }

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
        require_once dirname(__FILE__) . '/Island.php';

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
