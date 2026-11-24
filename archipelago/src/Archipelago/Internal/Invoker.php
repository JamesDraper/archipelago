<?php

/**
 * @internal
 */
class Archipelago_Internal_Invoker
{
    /**
     * @var Archipelago_Internal_ExceptionFactory_TypeExceptionFactory
     */
    private $typeExceptionFactory;

    public function __construct(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory $typeExceptionFactory)
    {
        $this->typeExceptionFactory = $typeExceptionFactory;
    }

    /**
     * @return Archipelago_Island_Outer_Response
     * @throws Archipelago_TypeException
     */
    public function invoke(
        Archipelago_Island_Boundary_Adapter $adapter,
        Archipelago_Island_Inner_Ingress $ingress,
        Archipelago_Island_Outer_Request $outerRequest
    ) {
        $innerRequest = $adapter->requestIn($outerRequest);
        // @phpstan-ignore instanceof.alwaysTrue
        if (!$innerRequest instanceof Archipelago_Island_Inner_Request) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $adapter,
                'requestIn',
                'Archipelago_Island_Inner_Request',
                false,
                $innerRequest
            );
        }

        $innerResponse = $ingress->invoke($innerRequest);
        // @phpstan-ignore instanceof.alwaysTrue
        if (!$innerResponse instanceof Archipelago_Island_Inner_Response) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $ingress,
                'invoke',
                'Archipelago_Island_Inner_Response',
                false,
                $innerResponse
            );
        }

        $outerResponse = $adapter->responseOut($innerResponse);
        // @phpstan-ignore instanceof.alwaysTrue
        if (!$outerResponse instanceof Archipelago_Island_Outer_Response) {
            throw $this->typeExceptionFactory->makeReturnObject(
                $adapter,
                'responseOut',
                'Archipelago_Island_Outer_Response',
                false,
                $outerResponse
            );
        }

        return $outerResponse;
    }
}
