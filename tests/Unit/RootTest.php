<?php
declare(strict_types=1);

namespace Tests\Unit;

use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Internal_IslandFactory;
use Archipelago_Island_Outer_Response;
use Archipelago_Island_Outer_Request;
use Archipelago_Island_Inner_Ingress;
use Archipelago_BadValueException;
use Archipelago_Internal_Registry;
use Archipelago_Internal_Invoker;
use Archipelago_Internal_Island;
use Archipelago_Internal_Root;
use Archipelago_Island;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class RootTest extends TestCase
{
    private Archipelago_Internal_Invoker&MockInterface $invoker;

    private Archipelago_Internal_IslandFactory&MockInterface $islandFactory;

    private Archipelago_Internal_Registry&MockInterface $unactivatedRegistry;

    private Archipelago_Internal_Registry&MockInterface $activatedRegistry;

    private Archipelago_Internal_Root $root;

    #[Test]
    public function it_registers_an_island(): void
    {
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $bootstrapper->expects()->onRegister();

        $island = Mockery::mock(Archipelago_Island::class);

        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland->expects()->getName()->andReturn('name');
        $internalIsland->expects()->getBootstrapper()->andReturn($bootstrapper);

        $this->islandFactory->expects()->make($island)->andReturn($internalIsland);

        $this->activatedRegistry->expects()->has('name')->andReturn(false);

        $this->unactivatedRegistry->expects()->has('name')->andReturn(false);
        $this->unactivatedRegistry->expects()->set('name', $internalIsland);

        $this->root->register($island);
    }

    #[Test]
    public function it_throws_when_registering_an_island_that_is_already_registered_unactivated(): void
    {
        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage('Island already registered named "name".');

        $island = Mockery::mock(Archipelago_Island::class);

        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland->expects()->getName()->andReturn('name');

        $this->islandFactory->expects()->make($island)->andReturn($internalIsland);

        $this->activatedRegistry->allows()->has('name')->andReturn(false);

        $this->unactivatedRegistry->allows()->has('name')->andReturn(true);

        $this->root->register($island);
    }

    #[Test]
    public function it_throws_when_registering_an_island_that_is_already_registered_activated(): void
    {
        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage('Island already registered named "name".');

        $island = Mockery::mock(Archipelago_Island::class);

        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland->expects()->getName()->andReturn('name');

        $this->islandFactory->expects()->make($island)->andReturn($internalIsland);

        $this->activatedRegistry->allows()->has('name')->andReturn(true);

        $this->unactivatedRegistry->allows()->has('name')->andReturn(false);        

        $this->root->register($island);
    }

    #[Test]
    public function it_activates_an_island_the_first_time_it_runs(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $bootstrapper->expects()->onFirstRun();

        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
        $configurator->expects()->configure($ingress);

        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland->expects()->getBootstrapper()->andReturn($bootstrapper);
        $internalIsland->expects()->getConfigurator()->andReturn($configurator);
        $internalIsland->expects()->getIngress()->twice()->andReturn($ingress);
        $internalIsland->expects()->getAdapter()->andReturn($adapter);

        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);

        $response = Mockery::mock(Archipelago_Island_Outer_Response::class);

        $this->unactivatedRegistry->expects()->has('name')->andReturn(true);
        $this->unactivatedRegistry->expects()->get('name')->andReturn($internalIsland);
        $this->unactivatedRegistry->expects()->remove('name');

        $this->activatedRegistry->expects()->set('name', $internalIsland);

        $this->invoker->expects()->invoke($adapter, $ingress, $request)->andReturn($response);

        $this->assertSame($response, $this->root->run('name', $request));
    }

    #[Test]
    public function it_runs_an_activated_island_without_activating_it_again(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland->expects()->getAdapter()->andReturn($adapter);
        $internalIsland->expects()->getIngress()->andReturn($ingress);

        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);

        $response = Mockery::mock(Archipelago_Island_Outer_Response::class);

        $this->activatedRegistry->expects()->has('name')->andReturn(true);
        $this->activatedRegistry->expects()->get('name')->andReturn($internalIsland);

        $this->unactivatedRegistry->expects()->has('name')->andReturn(false);

        $this->invoker->expects()->invoke($adapter, $ingress, $request)->andReturn($response);

        $this->assertSame($response, $this->root->run('name', $request));
    }

    #[Test]
    public function it_throws_when_running_an_island_that_is_not_registered(): void
    {
        $this->expectException(Archipelago_BadValueException::class);
        $this->expectExceptionMessage('No island registered named "name".');

        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);

        $this->activatedRegistry->expects()->has('name')->andReturn(false);

        $this->unactivatedRegistry->expects()->has('name')->andReturn(false);

        $this->invoker->shouldNotReceive('invoke');

        $this->root->run('name', $request);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoker = Mockery::mock(Archipelago_Internal_Invoker::class);
        $this->islandFactory = Mockery::mock(Archipelago_Internal_IslandFactory::class);
        $this->unactivatedRegistry = Mockery::mock(Archipelago_Internal_Registry::class);
        $this->activatedRegistry = Mockery::mock(Archipelago_Internal_Registry::class);

        $this->root = new Archipelago_Internal_Root(
            $this->unactivatedRegistry,
            $this->activatedRegistry,
            $this->islandFactory,
            $this->invoker,
        );
    }
}
