<?php

/**
 * @internal
 */
class Archipelago_Internal_Boundary_NullAdapter implements Archipelago_Island_Boundary_Adapter
{
    public function requestIn(Archipelago_Island_Outer_Request $request)
    {
        if (!$request instanceof Archipelago_Island_Inner_Request) {
            throw $this->makeException(
                $request,
                'Archipelago_Island_Inner_Request',
                'Archipelago_Island_Outer_Request'
            );
        }

        return $request;
    }

    public function responseOut(Archipelago_Island_Inner_Response $response)
    {
        if (!$response instanceof Archipelago_Island_Outer_Response) {
            throw $this->makeException(
                $response,
                'Archipelago_Island_Inner_Response',
                'Archipelago_Island_Outer_Response'
            );
        }

        return $response;
    }

    /**
     * @param object $object
     * @param string $innerInterface
     * @param string $outerInterface
     * @return Archipelago_TypeException
     */
    private function makeException($object, $innerInterface, $outerInterface)
    {
        $message = sprintf(
            '%s must implement both %s and %s when no adapter is used.',
            get_class($object),
            $innerInterface,
            $outerInterface
        );

        return new Archipelago_TypeException($message);
    }
}
