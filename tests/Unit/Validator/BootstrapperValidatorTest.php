<?php
declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_BootstrapperValidator;
use Archipelago_Internal_ExceptionFactory_TypeExceptionFactory;
use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_TypeException;
use Archipelago_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class BootstrapperValidatorTest extends TestCase
{
    private Archipelago_Internal_ExceptionFactory_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Validator_BootstrapperValidator $validator;

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_a_bootstrapper(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);

        $this->validator->assertValidType($island, $bootstrapper);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_null(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->validator->assertValidType($island, null);
    }

    #[Test]
    #[DataProvider('it_throws_when_the_bootstrapper_is_invalid_provider')]
    public function it_throws_when_the_bootstrapper_is_invalid(mixed $bootstrapper): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->assertThrowsFactoryException($island, $bootstrapper);
    }

    /**
     * @return array<string, array{bootstrapper: mixed}>
     */
    public static function it_throws_when_the_bootstrapper_is_invalid_provider(): array
    {
        return [
            'true' => ['bootstrapper' => true],
            'false' => ['bootstrapper' => false],
            'int' => ['bootstrapper' => 1],
            'zero' => ['bootstrapper' => 0],
            'float' => ['bootstrapper' => 1.5],
            'string' => ['bootstrapper' => 'Archipelago_Island_Boundary_Bootstrapper'],
            'empty string' => ['bootstrapper' => ''],
            'array' => ['bootstrapper' => []],
            'unrelated object' => ['bootstrapper' => new stdClass()],
        ];
    }

    #[Test]
    public function it_throws_when_the_bootstrapper_is_a_closure(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $bootstrapper = static function (): void {};

        $this->assertThrowsFactoryException($island, $bootstrapper);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::class);
        $this->validator = new Archipelago_Internal_Validator_BootstrapperValidator(
            $this->typeExceptionFactory,
        );
    }

    private function assertThrowsFactoryException(Archipelago_Island $island, mixed $bootstrapper): void
    {
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $this->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $island,
                'getBootstrapper',
                Archipelago_Island_Boundary_Bootstrapper::class,
                true,
                $bootstrapper,
            )
            ->andReturn($exception);

        try {
            $this->validator->assertValidType($island, $bootstrapper);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
