<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_NameValidator
{
    /**
     * @var Archipelago_Internal_ExceptionFactory_TypeExceptionFactory
     */
    private $typeExceptionFactory;

    /**
     * @var Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory
     */
    private $badValueExceptionFactory;

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
            throw $this->badValueExceptionFactory->makeException('Island name cannot be empty.');
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

            throw $this->badValueExceptionFactory->makeException(sprintf($message, $name));
        }
    }
}
