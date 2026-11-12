<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_NameValidator
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
            throw new Archipelago_BadValueException('Island name cannot be empty.');
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

            throw new Archipelago_BadValueException(sprintf($message, $name));
        }
    }
}
