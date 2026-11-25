<?php

declare(strict_types=1);

namespace Tests\Integration;

use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Island_Inner_Response;
use Archipelago_Island_Outer_Response;
use Archipelago_Island_Inner_Ingress;
use Archipelago_Island_Inner_Request;
use Archipelago_Island_Outer_Request;
use Archipelago_Internal_Root;
use Archipelago_Island;

use Mockery\MockInterface;
use Mockery;

use ReflectionProperty;

abstract class TestCase extends \Tests\TestCase
{
    /**
     * @return Archipelago_Island_Inner_Request&Archipelago_Island_Outer_Request&MockInterface
     */
    final protected function mockRequest()
    {
        return Mockery::mock(
            Archipelago_Island_Inner_Request::class . ',' . Archipelago_Island_Outer_Request::class,
        );
    }

    /**
     * @return Archipelago_Island_Inner_Response&Archipelago_Island_Outer_Response&MockInterface
     */
    final protected function mockResponse()
    {
        return Mockery::mock(
            Archipelago_Island_Inner_Response::class . ',' . Archipelago_Island_Outer_Response::class,
        );
    }

    final protected function mockInnerRequest(): Archipelago_Island_Inner_Request&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Inner_Request::class);
    }

    final protected function mockInnerResponse(): Archipelago_Island_Inner_Response&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Inner_Response::class);
    }

    final protected function mockOuterRequest(): Archipelago_Island_Outer_Request&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Outer_Request::class);
    }

    final protected function mockOuterResponse(): Archipelago_Island_Outer_Response&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Outer_Response::class);
    }

    final protected function mockIngress(): Archipelago_Island_Inner_Ingress&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Inner_Ingress::class);
    }

    final protected function mockBootstrapper(): Archipelago_Island_Boundary_Bootstrapper&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
    }

    final protected function mockConfigurator(): Archipelago_Island_Boundary_Configurator&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
    }

    final protected function mockAdapter(): Archipelago_Island_Boundary_Adapter&MockInterface
    {
        return Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
    }

    final protected function mockIsland(
        mixed $name,
        mixed $ingress,
        mixed $bootstrapper = null,
        mixed $configurator = null,
        mixed $adapter = null,
    ): Archipelago_Island&MockInterface {
        $island = Mockery::mock(Archipelago_Island::class);

        $island
            ->allows()
            ->getName()
            ->atMost()
            ->times(1)
            ->andReturn($name);

        $island
            ->allows()
            ->getIngress()
            ->atMost()
            ->times(1)
            ->andReturn($ingress);

        $island
            ->allows()
            ->getBootstrapper()
            ->atMost()
            ->times(1)
            ->andReturn($bootstrapper);

        $island
            ->allows()
            ->getConfigurator()
            ->atMost()
            ->times(1)
            ->andReturn($configurator);

        $island
            ->allows()
            ->getAdapter()
            ->atMost()
            ->times(1)
            ->andReturn($adapter);

        return $island;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        new ReflectionProperty(Archipelago_Internal_Root::class, 'instance')
            ->setValue(null, null);
    }
}
