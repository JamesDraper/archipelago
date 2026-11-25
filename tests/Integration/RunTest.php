<?php
declare(strict_types=1);

namespace Tests\Integration;

use Archipelago_BadValueException;
use Archipelago_TypeException;
use Archipelago;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use stdClass;

/**
 * @phpstan-type InvalidObjectScenario array{
 *     value: mixed,
 *     type: string
 * }
 */
final class RunTest extends TestCase
{
    #[Test]
    public function it_runs_an_island_through_its_adapter(): void
    {
        // Assert that the adapter is used to translate inner/outer
        // requests/responses if the island has one.

        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $innerRequest = $this->mockInnerRequest();
        $outerRequest = $this->mockOuterRequest();
        $innerResponse = $this->mockInnerResponse();
        $outerResponse = $this->mockOuterResponse();

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->globally()
            ->ordered()
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->globally()
            ->ordered()
            ->andReturn($innerResponse);

        $adapter
            ->expects()
            ->responseOut($innerResponse)
            ->globally()
            ->ordered()
            ->andReturn($outerResponse);

        Archipelago::register($this->mockIsland('island', $ingress, adapter: $adapter));

        $this->assertSame($outerResponse, Archipelago::run('island', $outerRequest));
    }

    #[Test]
    public function it_runs_an_island_without_an_adapter_when_messages_are_both_inner_and_outer(): void
    {
        // Assert that a request that is inner and outer and a response that is
        // inner and outer can be passed in/out of islands when an island
        // has no adapter.

        $request = $this->mockRequest();
        $response = $this->mockResponse();
        $ingress = $this->mockIngress();

        $ingress
            ->expects()
            ->invoke($request)
            ->andReturn($response);

        Archipelago::register($this->mockIsland('island', $ingress));

        $this->assertSame($response, Archipelago::run('island', $request));
    }

    #[Test]
    public function it_throws_when_running_an_island_that_is_not_registered(): void
    {
        // Assert that an exception is thrown when running an unregistered island.

        Archipelago::register($this->mockIsland('island_1', $this->mockIngress()));
        Archipelago::register($this->mockIsland('island_2', $this->mockIngress()));

        $request = $this->mockRequest();

        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage('No island registered named "island_3".');

        Archipelago::run('island_3', $request);
    }

    #[Test]
    public function it_routes_each_run_to_the_named_island(): void
    {
        // Assert that when multiple islands are defined,
        // the request is routed to the correct island.

        $ingress1 = $this->mockIngress();
        $ingress2 = $this->mockIngress();
        $ingress3 = $this->mockIngress();

        Archipelago::register($this->mockIsland('island_1', $ingress1));
        Archipelago::register($this->mockIsland('island_2', $ingress2));
        Archipelago::register($this->mockIsland('island_3', $ingress3));

        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $ingress2
            ->expects()
            ->invoke($request)
            ->andReturn($response);

        Archipelago::run('island_2', $request);
    }

    #[Test]
    public function it_runs_the_same_island_multiple_times_but_bootstrap_and_configurator_only_once(): void
    {
        // Assert that requests can be passed into the same island multiple times,
        // but the setup is only run the first time.

        $ingress = $this->mockIngress();
        $bootstrapper = $this->mockBootstrapper();
        $configurator = $this->mockConfigurator();
        $island = $this->mockIsland('island', $ingress, $bootstrapper, $configurator);
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $bootstrapper
            ->expects()
            ->onRegister();

        Archipelago::register($island);

        $bootstrapper
            ->expects()
            ->onFirstRun()
            ->globally()
            ->ordered();

        $configurator
            ->expects()
            ->configure($ingress)
            ->globally()
            ->ordered();

        $ingress
            ->expects()
            ->invoke($request)
            ->times(3)
            ->globally()
            ->ordered()
            ->andReturn($response);

        Archipelago::run('island', $request);
        Archipelago::run('island', $request);
        Archipelago::run('island', $request);
    }

    #[Test]
    #[DataProvider('invalid_objects_provider')]
    public function it_throws_when_the_adapter_does_not_return_an_inner_request(mixed $value, string $type): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Boundary_Adapter::requestIn()
        // does not return an instance of Archipelago_Island_Inner_Request.

        $outerRequest = $this->mockOuterRequest();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($value);

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::requestIn() should return an instance of Archipelago_Island_Inner_Request, got %s.',
            get_class($adapter),
            $type,
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    public function it_throws_when_the_adapter_returns_an_outer_request_from_request_in(): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Boundary_Adapter::requestIn()
        // returns an instance of Archipelago_Island_Outer_Request.

        $outerRequest = $this->mockOuterRequest();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($outerRequest);

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::requestIn() should return an instance of Archipelago_Island_Inner_Request, got object(%s).',
            get_class($adapter),
            get_class($outerRequest),
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    #[DataProvider('invalid_objects_provider')]
    public function it_throws_when_the_ingress_does_not_return_an_inner_response(mixed $value, string $type): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Inner_Ingress::invoke()
        // does not return an instance of Archipelago_Island_Inner_Response.

        $outerRequest = $this->mockOuterRequest();
        $innerRequest = $this->mockInnerRequest();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->andReturn($value);

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::invoke() should return an instance of Archipelago_Island_Inner_Response, got %s.',
            get_class($ingress),
            $type,
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    public function it_throws_when_the_ingress_returns_an_outer_response(): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Inner_Ingress::invoke()
        // returns an instance of Archipelago_Island_Outer_Response.

        $outerRequest = $this->mockOuterRequest();
        $innerRequest = $this->mockInnerRequest();
        $outerResponse = $this->mockOuterResponse();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

        $adapter
            ->expects()
            ->requestIn($outerRequest)
            ->andReturn($innerRequest);

        $ingress
            ->expects()
            ->invoke($innerRequest)
            ->andReturn($outerResponse);

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::invoke() should return an instance of Archipelago_Island_Inner_Response, got object(%s).',
            get_class($ingress),
            get_class($outerResponse),
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    #[DataProvider('invalid_objects_provider')]
    public function it_throws_when_the_adapter_does_not_return_an_outer_response(mixed $value, string $type): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Boundary_Adapter::responseOut()
        // does not return an instance of Archipelago_Island_Outer_Response.

        $outerRequest = $this->mockOuterRequest();
        $innerRequest = $this->mockInnerRequest();
        $innerResponse = $this->mockInnerResponse();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

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
            ->andReturn($value);

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::responseOut() should return an instance of Archipelago_Island_Outer_Response, got %s.',
            get_class($adapter),
            $type,
        ));

        Archipelago::run('island', $outerRequest);
    }

    /**
     * @return array<string, InvalidObjectScenario>
     */
    public static function invalid_objects_provider(): array
    {
        return [
            'null' => [
                'value' => null,
                'type' => 'null',
            ],
            'string' => [
                'value' => 'STRING',
                'type' => 'string',
            ],
            'int' => [
                'value' => 123,
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
    public function it_throws_when_the_adapter_returns_an_inner_response_from_response_out(): void
    {
        // Assert that a Archipelago_TypeException is thrown when Archipelago_Island_Boundary_Adapter::responseOut()
        // returns an instance of Archipelago_Island_Inner_Response.

        $outerRequest = $this->mockOuterRequest();
        $innerRequest = $this->mockInnerRequest();
        $innerResponse = $this->mockInnerResponse();
        $ingress = $this->mockIngress();
        $adapter = $this->mockAdapter();
        $island = $this->mockIsland('island', $ingress, null, null, $adapter);

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

        Archipelago::register($island);

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s::responseOut() should return an instance of Archipelago_Island_Outer_Response, got object(%s).',
            get_class($adapter),
            get_class($innerResponse),
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    public function it_throws_when_an_island_without_an_adapter_is_given_a_request_that_is_not_inner(): void
    {
        // Assert that when an adapter is not used, that the request must be both inner and outer.

        $outerRequest = $this->mockOuterRequest();
        $ingress = $this->mockIngress();

         Archipelago::register($this->mockIsland('island', $ingress));

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s must implement both Archipelago_Island_Inner_Request and '
                . 'Archipelago_Island_Outer_Request when no adapter is used.',
            get_class($outerRequest),
        ));

        Archipelago::run('island', $outerRequest);
    }

    #[Test]
    public function it_throws_when_an_island_without_an_adapter_returns_a_response_that_is_not_outer(): void
    {
        // Assert that when an adapter is not used, that the response must be both inner and outer.

        $request = $this->mockRequest();
        $response = $this->mockInnerResponse();
        $ingress = $this->mockIngress();

        $ingress
            ->expects()
            ->invoke($request)
            ->andReturn($response);

        Archipelago::register($this->mockIsland('island', $ingress));

        $this->expectException(Archipelago_TypeException::class);
        $this->expectExceptionMessage(sprintf(
            '%s must implement both Archipelago_Island_Inner_Response and Archipelago_Island_Outer_Response '
                . 'when no adapter is used.',
            get_class($response),
        ));

        Archipelago::run('island', $request);
    }
}
