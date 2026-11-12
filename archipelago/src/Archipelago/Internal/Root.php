<?php

/**
 * @internal
 */
class Archipelago_Internal_Root
{
    /**
     * @return self|null
     */
    private static $instance = null;

    /**
     * @var Archipelago_Internal_Registry
     */
    private $unactivatedRegistry;

    /**
     * @var Archipelago_Internal_Registry
     */
    private $activatedRegistry;

    /**
     * @var Archipelago_Internal_IslandFactory
     */
    private $islandFactory;

    /**
     * @var Archipelago_Internal_Invoker
     */
    private $invoker;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $typeExceptionFactory = new Archipelago_Internal_TypeExceptionFactory();
            $invoker = new Archipelago_Internal_Invoker($typeExceptionFactory);

            $validator = new Archipelago_Internal_Validator_Validator(
                new Archipelago_Internal_Validator_NameValidator($typeExceptionFactory),
                new Archipelago_Internal_Validator_IngressValidator($typeExceptionFactory),
                new Archipelago_Internal_Validator_BootstrapperValidator($typeExceptionFactory),
                new Archipelago_Internal_Validator_ConfiguratorValidator($typeExceptionFactory),
                new Archipelago_Internal_Validator_AdapterValidator($typeExceptionFactory)
            );

            $invoker = new Archipelago_Internal_Invoker($typeExceptionFactory);

            $islandFactory = new Archipelago_Internal_IslandFactory($validator);

            self::$instance = new self(
                new Archipelago_Internal_Registry(),
                new Archipelago_Internal_Registry(),
                $islandFactory,
                $invoker,
            );
        }

        return self::$instance;
    }

    public function __construct(
        Archipelago_Internal_Registry $unactivatedRegistry,
        Archipelago_Internal_Registry $activatedRegistry,
        Archipelago_Internal_IslandFactory $islandFactory,
        Archipelago_Internal_Invoker $invoker
    ) {
        $this->unactivatedRegistry = $unactivatedRegistry;
        $this->activatedRegistry = $activatedRegistry;
        $this->islandFactory = $islandFactory;
        $this->invoker = $invoker;
    }

    /**
     * @param string $island
     * @return Archipelago_Island_Outer_Response
     * @throws Archipelago_BadValueException
     * @throws Archipelago_TypeException
     */
    public function run($island, Archipelago_Island_Outer_Request $request)
    {
        $internalIsland = $this->getIsland($island);

        if (null === $internalIsland) {
            throw new Archipelago_BadValueException(sprintf('No island registered named "%s".', $island));
        }

        return $this->invoker->invoke($internalIsland->getAdapter(), $internalIsland->getIngress(), $request);
    }

    /**
     * @return void
     * @throws Archipelago_BadValueException
     * @throws Archipelago_TypeException
     */
    public function register(Archipelago_Island $island)
    {
        $internalIsland = $this->islandFactory->make($island);
        $name = $internalIsland->getName();

        if ($this->unactivatedRegistry->has($name) || $this->activatedRegistry->has($name)) {
            throw new Archipelago_BadValueException(sprintf('Island already registered named "%s".', $name));
        }

        $internalIsland->getBootstrapper()->onRegister();

        $this->unactivatedRegistry->set($name, $internalIsland);
    }

    /**
     * @param string $name
     * @return Archipelago_Internal_Island|null
     */
    private function getIsland($name)
    {
        if ($this->unactivatedRegistry->has($name)) {
            return $this->activateIsland($name);
        }

        if ($this->activatedRegistry->has($name)) {
            return $this->activatedRegistry->get($name);
        }

        return null;
    }

    /**
     * @param string $name
     * @return Archipelago_Internal_Island
     */
    private function activateIsland($name)
    {
        $internalIsland = $this->unactivatedRegistry->get($name);

        $internalIsland->getBootstrapper()->onFirstRun();

        $configurator = $internalIsland->getConfigurator();
        $ingress = $internalIsland->getIngress();

        $configurator->configure($ingress);

        $this->activatedRegistry->set($name, $internalIsland);
        $this->unactivatedRegistry->remove($name);

        return $internalIsland;
    }
}
