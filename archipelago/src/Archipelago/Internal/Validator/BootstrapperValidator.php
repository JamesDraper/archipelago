<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_BootstrapperValidator
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
     * @param mixed $bootstrapper
     * @return void
     * @throws Archipelago_TypeException
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
