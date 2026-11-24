<?php

/**
 * @internal
 */
class Archipelago_Internal_Boundary_NullAdapter implements Archipelago_Island_Boundary_Adapter
{
    /**
     * @var self|null
     */
    private static $instance = null;

    /**
     * @var Archipelago_Internal_ExceptionFactory_TypeExceptionFactory
     */
    private $typeExceptionFactory;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            require_once dirname(__FILE__) . '/../ExceptionFactory/TypeExceptionFactory.php';

            $typeExceptionFactory = Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::getInstance();

            self::$instance = new self($typeExceptionFactory);
        }

        return self::$instance;
    }

    public function __construct(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory $typeExceptionFactory)
    {
        $this->typeExceptionFactory = $typeExceptionFactory;
    }

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

        return $this->typeExceptionFactory->make($message);
    }
}
