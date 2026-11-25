<?php

declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_BootstrapperValidator;
use Archipelago_Internal_Validator_ConfiguratorValidator;
use Archipelago_Internal_Validator_AdapterValidator;
use Archipelago_Internal_Validator_IngressValidator;
use Archipelago_Internal_Validator_NameValidator;
use Archipelago_Internal_Validator_Validator;
use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Island_Inner_Ingress;
use Archipelago_BadValueException;
use Archipelago_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class ValidatorTest extends TestCase
{
    private Archipelago_Internal_Validator_NameValidator&MockInterface $nameValidator;

    private Archipelago_Internal_Validator_IngressValidator&MockInterface $ingressValidator;

    private Archipelago_Internal_Validator_BootstrapperValidator&MockInterface $bootstrapperValidator;

    private Archipelago_Internal_Validator_ConfiguratorValidator&MockInterface $configuratorValidator;

    private Archipelago_Internal_Validator_AdapterValidator&MockInterface $adapterValidator;

    private Archipelago_Internal_Validator_Validator $validator;

    #[Test]
    public function it_runs_every_validator(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $this
            ->nameValidator
            ->expects()
            ->assertValidType($island, 'name');

        $this
            ->nameValidator
            ->expects()
            ->assertNotEmpty('name');

        $this
            ->nameValidator
            ->expects()
            ->assertNameValid('name');

        $this
            ->ingressValidator
            ->expects()
            ->assertValidType($island, $ingress);

        $this
            ->bootstrapperValidator
            ->expects()
            ->assertValidType($island, $bootstrapper);

        $this
            ->configuratorValidator
            ->expects()
            ->assertValidType($island, $configurator);

        $this
            ->adapterValidator
            ->expects()
            ->assertValidType($island, $adapter);

        $this->validator->validate($island, 'name', $ingress, $bootstrapper, $configurator, $adapter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->nameValidator = Mockery::mock(Archipelago_Internal_Validator_NameValidator::class);
        $this->ingressValidator = Mockery::mock(Archipelago_Internal_Validator_IngressValidator::class);
        $this->bootstrapperValidator = Mockery::mock(Archipelago_Internal_Validator_BootstrapperValidator::class);
        $this->configuratorValidator = Mockery::mock(Archipelago_Internal_Validator_ConfiguratorValidator::class);
        $this->adapterValidator = Mockery::mock(Archipelago_Internal_Validator_AdapterValidator::class);

        $this->validator = new Archipelago_Internal_Validator_Validator(
            $this->nameValidator,
            $this->ingressValidator,
            $this->bootstrapperValidator,
            $this->configuratorValidator,
            $this->adapterValidator,
        );
    }
}
