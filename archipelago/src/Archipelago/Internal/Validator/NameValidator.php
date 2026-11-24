<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_NameValidator
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
     * @var Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory
     */
    private $badValueExceptionFactory;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            require_once dirname(__FILE__) . '/../ExceptionFactory/TypeExceptionFactory.php';
            require_once dirname(__FILE__) . '/../ExceptionFactory/BadValueExceptionFactory.php';

            $typeExceptionFactory = Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::getInstance();
            $badValueExceptionFactory = Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory::getInstance();

            self::$instance = new self($typeExceptionFactory, $badValueExceptionFactory);
        }

        return self::$instance;
    }

    public function __construct(
        Archipelago_Internal_ExceptionFactory_TypeExceptionFactory $typeExceptionFactory,
        Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory $badValueExceptionFactory
    ) {
        $this->typeExceptionFactory = $typeExceptionFactory;
        $this->badValueExceptionFactory = $badValueExceptionFactory;
    }

    /**
     * @param mixed $name
     * @return void
     * @throws Archipelago_TypeException
     */
    public function assertValidType(Archipelago_Island $island, $name)
    {
        if (!is_string($name)) {
            throw $this->typeExceptionFactory->makeReturnNonObject(
                $island,
                'getName',
                'string',
                false,
                $name
            );
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws Archipelago_BadValueException
     */
    public function assertNotEmpty($name)
    {
        if ('' === $name) {
            throw $this->badValueExceptionFactory->make('Island name cannot be empty.');
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws Archipelago_BadValueException
     */
    public function assertNameValid($name)
    {
        if (false === (bool) preg_match('~\A[a-z][a-z0-9._]*\z~', $name)) {
            $message = 'Island name must be lowercase letters, numbers, underscores, and dots only.'
                . ' It must begin with a lowercase letter. Got "%s".';

            throw $this->badValueExceptionFactory->make(sprintf($message, $name));
        }
    }
}
