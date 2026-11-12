<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_BootstrapperValidator
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
     * @param mixed $bootstrapper
     * @return void
     */
    public function assertValidType(Archipelago_Island $island, $bootstrapper)
    {
        if ((!$bootstrapper instanceof Archipelago_Island_Boundary_Bootstrapper) && ($bootstrapper !== null)) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $island,
                'getBootstrapper',
                'Archipelago_Island_Boundary_Bootstrapper',
                true,
                $bootstrapper
            );
        }
    }
}
