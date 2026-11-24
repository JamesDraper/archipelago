<?php
declare(strict_types=1);

namespace Tests\Unit\Boundary;

use Archipelago_Internal_ExceptionFactory_TypeExceptionFactory;
use Archipelago_Internal_Boundary_NullAdapter;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Island_Inner_Response;
use Archipelago_Island_Outer_Response;
use Archipelago_Island_Inner_Request;
use Archipelago_Island_Outer_Request;
use Archipelago_TypeException;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class NullAdapterTest extends TestCase
{
    private Archipelago_Internal_ExceptionFactory_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_Boundary_NullAdapter $adapter;

    #[Test]
    public function it_is_an_adapter(): void
    {
        $this->assertInstanceOf(Archipelago_Island_Boundary_Adapter::class, $this->adapter);
    }

    #[Test]
    public function it_passes_through_a_request_that_is_both_outer_and_inner(): void
    {
        $requestType = Archipelago_Island_Outer_Request::class . ', ' . Archipelago_Island_Inner_Request::class;
        $request = Mockery::mock($requestType);

        $this->assertSame($request, $this->adapter->requestIn($request));
    }

    #[Test]
    public function it_throws_when_a_request_is_only_outer(): void
    {
        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $expectedMessage = get_class($request) . ' must implement both Archipelago_Island_Inner_Request and'
            . ' Archipelago_Island_Outer_Request when no adapter is used.';

        $this
            ->typeExceptionFactory
            ->expects()
            ->make($expectedMessage)
            ->andReturn($exception);

        try {
            $this->adapter->requestIn($request);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }

    #[Test]
    public function it_passes_through_a_response_that_is_both_inner_and_outer(): void
    {
        $responseType = Archipelago_Island_Inner_Response::class . ', ' . Archipelago_Island_Outer_Response::class;
        $response = Mockery::mock($responseType);

        $this->assertSame($response, $this->adapter->responseOut($response));
    }

    #[Test]
    public function it_throws_when_a_response_is_only_inner(): void
    {
        $response = Mockery::mock(Archipelago_Island_Inner_Response::class);
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $expectedMessage = get_class($response) . ' must implement both Archipelago_Island_Inner_Response and'
            . ' Archipelago_Island_Outer_Response when no adapter is used.';

        $this
            ->typeExceptionFactory
            ->expects()
            ->make($expectedMessage)
            ->andReturn($exception);

        try {
            $this->adapter->responseOut($response);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::class);

        $this->adapter = new Archipelago_Internal_Boundary_NullAdapter($this->typeExceptionFactory);
    }
}
