<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_IngressValidator
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
     * @param mixed $ingress
     * @return void
     * @throws Archipelago_TypeException
     */
    public function assertValidType(Archipelago_Island $island, $ingress)
    {
        if (!$ingress instanceof Archipelago_Island_Inner_Ingress) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $island,
                'getIngress',
                'Archipelago_Island_Inner_Ingress',
                false,
                $ingress
            );
        }
    }
}
