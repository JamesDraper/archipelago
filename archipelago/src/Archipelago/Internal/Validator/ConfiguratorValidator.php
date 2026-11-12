<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_ConfiguratorValidator
{
    /**
     * @var Archipelago_Internal_TypeExceptionFactory
     */
    private $typeExceptionFactory;

    public function __construct(Archipelago_Internal_TypeExceptionFactory $typeExceptionFactory)
    {
        $this->typeExceptionFactory = $typeExceptionFactory;
    }

    /**
     * @param mixed $configurator
     * @return void
     */
    public function assertValidType(Archipelago_Island $island, $configurator)
    {
        if ((!$configurator instanceof Archipelago_Island_Boundary_Configurator) && ($configurator !== null)) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $island,
                'getConfigurator',
                'Archipelago_Island_Boundary_Configurator',
                true,
                $configurator
            );
        }
    }
}
