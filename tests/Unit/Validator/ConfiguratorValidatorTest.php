<?php
declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_ConfiguratorValidator;
use Archipelago_Internal_TypeExceptionFactory;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_TypeException;
use Archipelago_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class ConfiguratorValidatorTest extends TestCase
{
    private Archipelago_Internal_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Validator_ConfiguratorValidator $validator;

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_a_configurator(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);

        $this->validator->assertValidType($island, $configurator);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_null(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->validator->assertValidType($island, null);
    }

    #[Test]
    #[DataProvider('it_throws_when_the_configurator_is_invalid_provider')]
    public function it_throws_when_the_configurator_is_invalid(mixed $configurator): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->assertThrowsFactoryException($island, $configurator);
    }

    /**
     * @return array<string, array{configurator: mixed}>
     */
    public static function it_throws_when_the_configurator_is_invalid_provider(): array
    {
        return [
            'true' => ['configurator' => true],
            'false' => ['configurator' => false],
            'int' => ['configurator' => 1],
            'zero' => ['configurator' => 0],
            'float' => ['configurator' => 1.5],
            'string' => ['configurator' => 'Archipelago_Island_Boundary_Configurator'],
            'empty string' => ['configurator' => ''],
            'array' => ['configurator' => []],
            'unrelated object' => ['configurator' => new stdClass()],
        ];
    }

    #[Test]
    public function it_throws_when_the_configurator_is_a_closure(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $configurator = static function (): void {};

        $this->assertThrowsFactoryException($island, $configurator);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_TypeExceptionFactory::class);
        $this->validator = new Archipelago_Internal_Validator_ConfiguratorValidator(
            $this->typeExceptionFactory,
        );
    }

    private function assertThrowsFactoryException(Archipelago_Island $island, mixed $configurator): void
    {
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $this->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $island,
                'getConfigurator',
                Archipelago_Island_Boundary_Configurator::class,
                true,
                $configurator,
            )
            ->andReturn($exception);

        try {
            $this->validator->assertValidType($island, $configurator);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
