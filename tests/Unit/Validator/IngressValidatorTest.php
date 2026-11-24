<?php
declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_IngressValidator;
use Archipelago_Internal_TypeExceptionFactory;
use Archipelago_Island_Inner_Ingress;
use Archipelago_Island_Inner_Request;
use Archipelago_TypeException;
use Archipelago_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class IngressValidatorTest extends TestCase
{
    private Archipelago_Internal_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Validator_IngressValidator $validator;

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_an_ingress(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $this->validator->assertValidType($island, $ingress);
    }

    #[Test]
    #[DataProvider('it_throws_when_the_ingress_is_invalid_provider')]
    public function it_throws_when_the_ingress_is_invalid(mixed $ingress): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->assertThrowsFactoryException($island, $ingress);
    }

    /**
     * @return array<string, array{ingress: mixed}>
     */
    public static function it_throws_when_the_ingress_is_invalid_provider(): array
    {
        return [
            'null' => ['ingress' => null],
            'bool' => ['ingress' => true],
            'int' => ['ingress' => 1],
            'float' => ['ingress' => 1.5],
            'string' => ['ingress' => 'Archipelago_Island_Inner_Ingress'],
            'array' => ['ingress' => []],
            'unrelated object' => ['ingress' => new stdClass()],
        ];
    }

    #[Test]
    public function it_throws_when_the_ingress_is_a_closure(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $ingress = static function (): void {};

        $this->assertThrowsFactoryException($island, $ingress);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_TypeExceptionFactory::class);
        $this->validator = new Archipelago_Internal_Validator_IngressValidator(
            $this->typeExceptionFactory,
        );
    }

    private function assertThrowsFactoryException(Archipelago_Island $island, mixed $ingress): void
    {
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $this->typeExceptionFactory
            ->expects()
            ->makeReturnObject($island, 'getIngress', Archipelago_Island_Inner_Ingress::class, false, $ingress)
            ->andReturn($exception);

        try {
            $this->validator->assertValidType($island, $ingress);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
