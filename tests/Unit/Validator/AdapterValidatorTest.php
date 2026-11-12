<?php
declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_AdapterValidator;
use Archipelago_Internal_TypeExceptionFactory;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_TypeException;
use Archipelago_Island;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class AdapterValidatorTest extends TestCase
{
    private Archipelago_Internal_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Validator_AdapterValidator $validator;

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_an_adapter(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $this->validator->assertValidType($island, $adapter);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_null(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->validator->assertValidType($island, null);
    }

    #[Test]
    #[DataProvider('it_throws_when_the_adapter_is_invalid_provider')]
    public function it_throws_when_the_adapter_is_invalid(mixed $adapter): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->assertThrowsFactoryException($island, $adapter);
    }

    /**
     * @return array<string, array{adapter: mixed}>
     */
    public static function it_throws_when_the_adapter_is_invalid_provider(): array
    {
        return [
            'true' => ['adapter' => true],
            'false' => ['adapter' => false],
            'int' => ['adapter' => 1],
            'zero' => ['adapter' => 0],
            'float' => ['adapter' => 1.5],
            'string' => ['adapter' => 'Archipelago_Island_Boundary_Adapter'],
            'empty string' => ['adapter' => ''],
            'array' => ['adapter' => []],
            'unrelated object' => ['adapter' => new stdClass()],
        ];
    }

    #[Test]
    public function it_throws_when_the_adapter_is_a_closure(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $adapter = static function (): void {};

        $this->assertThrowsFactoryException($island, $adapter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_TypeExceptionFactory::class);
        $this->validator = new Archipelago_Internal_Validator_AdapterValidator(
            $this->typeExceptionFactory,
        );
    }

    private function assertThrowsFactoryException(Archipelago_Island $island, mixed $adapter): void
    {
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $this->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $island,
                'getAdapter',
                Archipelago_Island_Boundary_Adapter::class,
                true,
                $adapter,
            )
            ->andReturn($exception);

        try {
            $this->validator->assertValidType($island, $adapter);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
