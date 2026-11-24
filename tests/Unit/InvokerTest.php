<?php
declare(strict_types=1);

namespace Tests\Unit;

use Archipelago_Internal_ExceptionFactory_TypeExceptionFactory;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Island_Outer_Response;
use Archipelago_Island_Inner_Response;
use Archipelago_Island_Outer_Request;
use Archipelago_Island_Inner_Request;
use Archipelago_Island_Inner_Ingress;
use Archipelago_Internal_Invoker;
use Archipelago_TypeException;


use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class InvokerTest extends TestCase
{
    private Archipelago_Internal_ExceptionFactory_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Invoker $invoker;

    #[Test]
    public function it_invokes_the_ingress_through_the_adapter(): void
    {
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $outerRequest = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $innerRequest = Mockery::mock(Archipelago_Island_Inner_Request::class);
        $innerResponse = Mockery::mock(Archipelago_Island_Inner_Response::class);
        $outerResponse = Mockery::mock(Archipelago_Island_Outer_Response::class);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->andReturn($innerResponse);

        $adapter
            ->expects()
            ->responseOut($innerResponse)
            ->andReturn($outerResponse);

        $this->assertSame($outerResponse, $this->invoker->invoke($adapter, $ingress, $outerRequest));
    }

    #[Test]
    public function it_throws_when_the_adapter_does_not_return_an_inner_request(): void
    {
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $outerRequest = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($outerRequest);

        $this
            ->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $adapter,
                'requestIn',
                Archipelago_Island_Inner_Request::class,
                false,
                $outerRequest,
            )
            ->andReturn($exception);

        $this->assertInvokeThrows($adapter, $ingress, $outerRequest, $exception);
    }

    #[Test]
    public function it_throws_when_the_ingress_does_not_return_an_inner_response(): void
    {
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $outerRequest = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $innerRequest = Mockery::mock(Archipelago_Island_Inner_Request::class);
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->andReturn($innerRequest);

        $this
            ->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $ingress,
                'invoke',
                Archipelago_Island_Inner_Response::class,
                false,
                $innerRequest,
            )
            ->andReturn($exception);

        $this->assertInvokeThrows($adapter, $ingress, $outerRequest, $exception);
    }

    #[Test]
    public function it_throws_when_the_adapter_does_not_return_an_outer_response(): void
    {
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $outerRequest = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $innerRequest = Mockery::mock(Archipelago_Island_Inner_Request::class);
        $innerResponse = Mockery::mock(Archipelago_Island_Inner_Response::class);
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->andReturn($innerResponse);

        $adapter
            ->expects()
            ->responseOut($innerResponse)
            ->andReturn($innerResponse);

        $this
            ->typeExceptionFactory
            ->expects()
            ->makeReturnObject(
                $adapter,
                'responseOut',
                Archipelago_Island_Outer_Response::class,
                false,
                $innerResponse,
            )
            ->andReturn($exception);

        $this->assertInvokeThrows($adapter, $ingress, $outerRequest, $exception);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::class);

        $this->invoker = new Archipelago_Internal_Invoker($this->typeExceptionFactory);
    }

    private function assertInvokeThrows(
        Archipelago_Island_Boundary_Adapter $adapter,
        Archipelago_Island_Inner_Ingress $ingress,
        Archipelago_Island_Outer_Request $outerRequest,
        Archipelago_TypeException $exception,
    ): void {
        try {
            $this->invoker->invoke($adapter, $ingress, $outerRequest);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
