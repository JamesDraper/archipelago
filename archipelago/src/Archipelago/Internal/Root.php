<?php

/**
 * @internal
 */
class Archipelago_Internal_Root
{
    /**
     * @var self|null
     */
    private static $instance = null;

    /**
     * @var Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory
     */
    private $badValueExceptionFactory;

    /**
     * @var Archipelago_Internal_Registry_UnactivatedIslandRegistry
     */
    private $unactivatedRegistry;

    /**
     * @var Archipelago_Internal_Registry_ActivatedIslandRegistry
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
            require_once dirname(__FILE__) . '/ExceptionFactory/BadValueExceptionFactory.php';
            require_once dirname(__FILE__) . '/Registry/Registry.php';
            require_once dirname(__FILE__) . '/Registry/UnactivatedIslandRegistry.php';
            require_once dirname(__FILE__) . '/Registry/ActivatedIslandRegistry.php';
            require_once dirname(__FILE__) . '/IslandFactory.php';
            require_once dirname(__FILE__) . '/Invoker.php';

            $badValueExceptionFactory = Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory::getInstance();
            $unactivatedIslandRegistry = Archipelago_Internal_Registry_UnactivatedIslandRegistry::getInstance();
            $activatedIslandRegistry = Archipelago_Internal_Registry_ActivatedIslandRegistry::getInstance();
            $islandFactory = Archipelago_Internal_IslandFactory::getInstance();
            $invoker = Archipelago_Internal_Invoker::getInstance();

            self::$instance = new self(
                $badValueExceptionFactory,
                $unactivatedIslandRegistry,
                $activatedIslandRegistry,
                $islandFactory,
                $invoker
            );
        }

        return self::$instance;
    }

    public function __construct(
        Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory $badValueExceptionFactory,
        Archipelago_Internal_Registry_UnactivatedIslandRegistry $unactivatedRegistry,
        Archipelago_Internal_Registry_ActivatedIslandRegistry $activatedRegistry,
        Archipelago_Internal_IslandFactory $islandFactory,
        Archipelago_Internal_Invoker $invoker
    ) {
        $this->badValueExceptionFactory = $badValueExceptionFactory;
        $this->unactivatedRegistry = $unactivatedRegistry;
        $this->activatedRegistry = $activatedRegistry;
        $this->islandFactory = $islandFactory;
        $this->invoker = $invoker;
    }

    /**
     * @param string $name
     * @return Archipelago_Island_Outer_Response
     * @throws Archipelago_BadValueException
     * @throws Archipelago_TypeException
     */
    public function run($name, Archipelago_Island_Outer_Request $request)
    {
        $unactivatedIsland = $this->unactivatedRegistry->get($name);

        if (!is_null($unactivatedIsland)) {
            $this->activatedRegistry->add($unactivatedIsland);
            $this->unactivatedRegistry->remove($name);

            $activatedIsland = $unactivatedIsland;
        } else {
            $activatedIsland = $this->activatedRegistry->get($name);

            if (is_null($activatedIsland)) {
                throw $this->badValueExceptionFactory->make(sprintf(
                    'No island registered named "%s".',
                    $name
                ));
            }
        }

        return $this->invoker->invoke(
            $activatedIsland->getAdapter(),
            $activatedIsland->getIngress(),
            $request
        );
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

        if (!is_null($this->unactivatedRegistry->get($name)) || !is_null($this->activatedRegistry->get($name))) {
            throw $this->badValueExceptionFactory->make(sprintf(
                'Island already registered named "%s".',
                $name
            ));
        }

        $this->unactivatedRegistry->add($internalIsland);
    }
}
