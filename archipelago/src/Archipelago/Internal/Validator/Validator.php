<?php

/**
 * @internal
 */
class Archipelago_Internal_Validator_Validator
{
    /**
     * @var Archipelago_Internal_Validator_NameValidator
     */
    private $nameValidator;

    /**
     * @var Archipelago_Internal_Validator_IngressValidator
     */
    private $ingressValidator;

    /**
     * @var Archipelago_Internal_Validator_BootstrapperValidator
     */
    private $bootstrapperValidator;

    /**
     * @var Archipelago_Internal_Validator_ConfiguratorValidator
     */
    private $configuratorValidator;

    /**
     * @var Archipelago_Internal_Validator_AdapterValidator
     */
    private $adapterValidator;

    public function __construct(
        Archipelago_Internal_Validator_NameValidator $nameValidator,
        Archipelago_Internal_Validator_IngressValidator $ingressValidator,
        Archipelago_Internal_Validator_BootstrapperValidator $bootstrapperValidator,
        Archipelago_Internal_Validator_ConfiguratorValidator $configuratorValidator,
        Archipelago_Internal_Validator_AdapterValidator $adapterValidator
    ) {
        $this->nameValidator = $nameValidator;
        $this->ingressValidator = $ingressValidator;
        $this->bootstrapperValidator = $bootstrapperValidator;
        $this->configuratorValidator = $configuratorValidator;
        $this->adapterValidator = $adapterValidator;
    }

    /**
     * @param mixed $name
     * @param mixed $ingress
     * @param mixed $bootstrapper
     * @param mixed $configurator
     * @param mixed $adapter
     * @return void
     * @throws Archipelago_BadValueException
     * @throws Archipelago_TypeException
     */
    public function validate(Archipelago_Island $island, $name, $ingress, $bootstrapper, $configurator, $adapter)
    {
        $this->nameValidator->assertValidType($island, $name);

        /**
         * @var string $name
         */

        $this->nameValidator->assertNotEmpty($name);
        $this->nameValidator->assertNameValid($name);
        $this->ingressValidator->assertValidType($island, $ingress);
        $this->bootstrapperValidator->assertValidType($island, $bootstrapper);
        $this->configuratorValidator->assertValidType($island, $configurator);
        $this->adapterValidator->assertValidType($island, $adapter);
    }
}
