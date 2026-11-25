<?php

declare(strict_types=1);

namespace Tests\Integration;

use Archipelago_BadValueException;
use Archipelago_TypeException;
use Archipelago;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use stdClass;

use function get_class;
use function sprintf;

/**
 * Tests that assert that an island can be registered properly, and
 * throws exceptions if an island fails validation.
 *
 * @phpstan-type NameScenario array{
 *     name: string,
 * }
 * @phpstan-type ValueTypeScenario array{
 *     value: mixed,
 *     type: string,
 * }
 */
final class RegisterTest extends TestCase
{
    #[Test]
    #[DataProvider('it_registers_an_island_with_a_valid_name_provider')]
    public function it_registers_an_island_with_a_valid_name(string $name): void
    {
        // Assert that an island can be registed with a valid name.

        $island = $this->mockIsland($name, $this->mockIngress());

        Archipelago::register($island);
    }

    /**
     * @return array<string, NameScenario>
     */
    public static function it_registers_an_island_with_a_valid_name_provider(): array
    {
        return [
            'single letter' => [
                'name' => 'a',
            ],
            'letters' => [
                'name' => 'name',
            ],
            'letters and numbers' => [
                'name' => 'name1',
            ],
            'underscores' => [
                'name' => 'a_name',
            ],
            'dots' => [
                'name' => 'a.name',
            ],
            'dots and underscores' => [
                'name' => 'a.island_name',
            ],
        ];
    }

    #[Test]
    public function it_registers_an_island_with_a_valid_bootstrapper(): void
    {
        // Assert that an island can be registed with a valid bootstrapper.

        $bootstrapper = $this->mockBootstrapper();
        $bootstrapper
            ->expects()
            ->onRegister();

        $island = $this->mockIsland('island', $this->mockIngress(), $bootstrapper);

        Archipelago::register($island);
    }

    #[Test]
    public function it_registers_an_island_with_a_valid_configurator(): void
    {
        // Assert that an island can be registed with a valid configurator.

        $island = $this->mockIsland('island', $this->mockIngress(), configurator: $this->mockConfigurator());

        Archipelago::register($island);
    }

    #[Test]
    public function it_registers_an_island_with_a_valid_adapter(): void
    {
        // Assert that an island can be registed with a valid adapter.

        $island = $this->mockIsland('island', $this->mockIngress(), adapter: $this->mockAdapter());

        Archipelago::register($island);
    }

    #[Test]
    #[DataProvider('it_rejects_an_island_with_an_invalid_name_provider')]
    public function it_rejects_an_island_with_an_invalid_name(string $name): void
    {
        // Assert that registering an island with an invalid name throws an Archipelago_BadValueException.

        $island = $this->mockIsland($name, $this->mockIngress());

        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage(sprintf(
            'Island name must be lowercase letters, numbers, underscores, and dots only.'
                . ' It must begin with a lowercase letter. Got "%s".',
            $name,
        ));

        Archipelago::register($island);
    }

    /**
     * @return array<string, NameScenario>
     */
    public static function it_rejects_an_island_with_an_invalid_name_provider(): array
    {
        return [
            'uppercase' => [
                'name' => 'A',
            ],
            'leading number' => [
                'name' => '1a',
            ],
            'leading dot' => [
                'name' => '.a',
            ],
            'leading underscore' => [
                'name' => '_a',
            ],
            'space' => [
                'name' => 'island name',
            ],
            'slash' => [
                'name' => 'island/name',
            ],
            'trailing newline' => [
                'name' => "island\n",
            ],
        ];
    }

    #[Test]
    public function it_rejects_an_island_with_an_empty_name(): void
    {
        // Assert that registering an island with an empty name throws an Archipelago_BadValueException.

        $island = $this->mockIsland('', $this->mockIngress());

        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage('Island name cannot be empty.');

        Archipelago::register($island);
    }

    #[Test]
    #[DataProvider('it_rejects_an_island_whose_name_is_not_a_string_provider')]
    public function it_rejects_an_island_whose_name_is_not_a_string(mixed $value, string $type): void
    {
        // Assert that registering an island with an non-string name throws an Archipelago_TypeValueException.

        $island = $this->mockIsland($value, $this->mockIngress());

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(get_class($island) . "::getName() should return string, got {$type}.");

        Archipelago::register($island);
    }

    /**
     * @return array<string, ValueTypeScenario>
     */
    public static function it_rejects_an_island_whose_name_is_not_a_string_provider(): array
    {
        return [
            'null' => [
                'value' => null,
                'type' => 'null',
            ],
            'int' => [
                'value' => 1,
                'type' => 'int',
            ],
            'array' => [
                'value' => [1, 2, 3],
                'type' => 'array',
            ],
            'object' => [
                'value' => new stdClass(),
                'type' => 'object(stdClass)',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_rejects_an_island_with_an_invalid_ingress_provider')]
    public function it_rejects_an_island_with_an_invalid_ingress(mixed $value, string $type): void
    {
        // Assert that registering an island with an non Archipelago_Island_Inner_Ingress object
        // throws an Archipelago_TypeValueException.

        $island = $this->mockIsland('island', $value);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(
            get_class($island) . '::getIngress() should return an instance of Archipelago_Island_Inner_Ingress,'
            . " got {$type}.",
        );

        Archipelago::register($island);
    }

    /**
     * @return array<string, ValueTypeScenario>
     */
    public static function it_rejects_an_island_with_an_invalid_ingress_provider(): array
    {
        return [
            'int' => [
                'value' => 1,
                'type' => 'int',
            ],
            'string' => [
                'value' => 'STRING',
                'type' => 'string',
            ],
            'array' => [
                'value' => [1, 2, 3],
                'type' => 'array',
            ],
            'object' => [
                'value' => new stdClass(),
                'type' => 'object(stdClass)',
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalid_bootstrapper_configurator_adapter_objects_provider')]
    public function it_rejects_an_island_with_an_invalid_bootstrapper(mixed $value, string $type): void
    {
        // Assert that registering an island with an non Archipelago_Island_Boundary_Bootstrapper object or null
        // throws an Archipelago_TypeValueException.

        $island = $this->mockIsland('island', $this->mockIngress(), $value);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::getBootstrapper() should return an instance of Archipelago_Island_Boundary_Bootstrapper or null, got %s',
            get_class($island),
            $type,
        ));

        Archipelago::register($island);
    }

    #[Test]
    #[DataProvider('invalid_bootstrapper_configurator_adapter_objects_provider')]
    public function it_rejects_an_island_with_an_invalid_configurator(mixed $value, string $type): void
    {
        // Assert that registering an island with an non Archipelago_Island_Boundary_Configurator object or null
        // throws an Archipelago_TypeValueException.

        $island = $this->mockIsland('island', $this->mockIngress(), configurator: $value);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::getConfigurator() should return an instance of Archipelago_Island_Boundary_Configurator or null, got %s',
            get_class($island),
            $type,
        ));

        Archipelago::register($island);
    }

    #[Test]
    #[DataProvider('invalid_bootstrapper_configurator_adapter_objects_provider')]
    public function it_rejects_an_island_with_an_invalid_adapter(mixed $value, string $type): void
    {
        // Assert that registering an island with an non Archipelago_Island_Boundary_Adapter object or null
        // throws an Archipelago_TypeValueException.

        $island = $this->mockIsland('island', $this->mockIngress(), adapter: $value);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::getAdapter() should return an instance of Archipelago_Island_Boundary_Adapter or null, got %s',
            get_class($island),
            $type,
        ));

        Archipelago::register($island);
    }

    /**
     * @return array<string, ValueTypeScenario>
     */
    public static function invalid_bootstrapper_configurator_adapter_objects_provider(): array
    {
        return [
            'int' => [
                'value' => 1,
                'type' => 'int',
            ],
            'string' => [
                'value' => 'STRING',
                'type' => 'string',
            ],
            'array' => [
                'value' => [1, 2, 3],
                'type' => 'array',
            ],
            'object' => [
                'value' => new stdClass(),
                'type' => 'object(stdClass)',
            ],
        ];
    }
}
