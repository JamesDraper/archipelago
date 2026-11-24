<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_IngressValidator
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
