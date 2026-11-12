<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_AdapterValidator
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
     * @param mixed $adapter
     * @return void
     * @throws Archipelago_TypeException
     */
    public function assertValidType(Archipelago_Island $island, $adapter)
    {
        if ((!$adapter instanceof Archipelago_Island_Boundary_Adapter) && ($adapter !== null)) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $island,
                'getAdapter',
                'Archipelago_Island_Boundary_Adapter',
                true,
                $adapter
            );
        }
    }
}
