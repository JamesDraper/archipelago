<?php
declare(strict_types=1);

namespace Tests\Unit;

use Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory;
use Archipelago_Internal_Registry_UnactivatedIslandRegistry;
use Archipelago_Internal_Registry_ActivatedIslandRegistry;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Internal_IslandFactory;
use Archipelago_Island_Outer_Response;
use Archipelago_Island_Outer_Request;
use Archipelago_Island_Inner_Ingress;
use Archipelago_BadValueException;
use Archipelago_Internal_Invoker;
use Archipelago_Internal_Island;
use Archipelago_Internal_Root;
use Archipelago_Island;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class RootTest extends TestCase
{
    private Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory&MockInterface $badValueExceptionFactory;

    private Archipelago_Internal_Registry_UnactivatedIslandRegistry&MockInterface $unactivatedRegistry;

    private Archipelago_Internal_Registry_ActivatedIslandRegistry&MockInterface $activatedRegistry;

    private Archipelago_Internal_IslandFactory&MockInterface $islandFactory;

    private Archipelago_Internal_Invoker&MockInterface $invoker;

    private Archipelago_Internal_Root $root;

    #[Test]
    public function it_registers_an_island(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);

        $this
            ->islandFactory
            ->expects()
            ->make($island)
            ->andReturn($internalIsland);

        $internalIsland
            ->expects()
            ->getName()
            ->andReturn('island');

        $this
            ->activatedRegistry
            ->allows()
            ->get('island')
            ->andReturn(null);

        $this
            ->unactivatedRegistry
            ->allows()
            ->get('island')
            ->andReturn(null);
        
        $this
            ->unactivatedRegistry
            ->expects()
            ->add($internalIsland);

        $this->root->register($island);
    }

    #[Test]
    public function it_throws_when_registering_an_island_that_is_already_registered_unactivated(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $existingIsland = Mockery::mock(Archipelago_Internal_Island::class);

        $this
            ->islandFactory
            ->expects()
            ->make($island)
            ->andReturn($internalIsland);

        $internalIsland
            ->expects()
            ->getName()
            ->andReturn('island');

        $this
            ->activatedRegistry
            ->allows()
            ->get('island')
            ->andReturn(null);

        $this
            ->unactivatedRegistry
            ->allows()
            ->get('island')
            ->andReturn($existingIsland);

        $this->assertThrowsFactoryException(
            'Island already registered named "island".',
            fn () => $this->root->register($island),
        );
    }

    #[Test]
    public function it_throws_when_registering_an_island_that_is_already_registered_activated(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $existingIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);

        $this
            ->islandFactory
            ->expects()
            ->make($island)
            ->andReturn($internalIsland);

        $internalIsland
            ->expects()
            ->getName()
            ->andReturn('island');

        $this
            ->activatedRegistry
            ->allows()
            ->get('island')
            ->andReturn($existingIsland);

        $this
            ->unactivatedRegistry
            ->allows()
            ->get('island')
            ->andReturn(null);

        $this->assertThrowsFactoryException(
            'Island already registered named "island".',
            fn () => $this->root->register($island),
        );
    }

    #[Test]
    public function it_activates_an_island_the_first_time_it_runs(): void
    {
        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $response = Mockery::mock(Archipelago_Island_Outer_Response::class);

        $internalIsland
            ->expects()
            ->getAdapter()
            ->andReturn($adapter);
        
        $internalIsland
            ->expects()
            ->getIngress()
            ->andReturn($ingress);

        $this
            ->unactivatedRegistry
            ->expects()
            ->get('island')
            ->andReturn($internalIsland);
        
        $this
            ->unactivatedRegistry
            ->expects()
            ->remove('island');

        $this
            ->activatedRegistry
            ->expects()
            ->add($internalIsland);

        $this
            ->invoker
            ->expects()
            ->invoke($adapter, $ingress, $request)
            ->andReturn($response);

        $this->assertSame($response, $this->root->run('island', $request));
    }

    #[Test]
    public function it_runs_an_activated_island_without_activating_it_again(): void
    {
        $internalIsland = Mockery::mock(Archipelago_Internal_Island::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);
        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);
        $response = Mockery::mock(Archipelago_Island_Outer_Response::class);

        $internalIsland
            ->expects()
            ->getAdapter()
            ->andReturn($adapter);
        
        $internalIsland
            ->expects()
            ->getIngress()
            ->andReturn($ingress);

        $this
            ->unactivatedRegistry
            ->expects()
            ->get('island')
            ->andReturn(null);

        $this
            ->activatedRegistry
            ->expects()
            ->get('island')
            ->andReturn($internalIsland);

        $this
            ->invoker
            ->expects()
            ->invoke($adapter, $ingress, $request)
            ->andReturn($response);

        $this->assertSame($response, $this->root->run('island', $request));
    }

    #[Test]
    public function it_throws_when_running_an_island_that_is_not_registered(): void
    {
        $request = Mockery::mock(Archipelago_Island_Outer_Request::class);

        $this
            ->unactivatedRegistry
            ->expects()
            ->get('island')
            ->andReturn(null);

        $this
            ->activatedRegistry
            ->expects()
            ->get('island')
            ->andReturn(null);

        $this->assertThrowsFactoryException(
            'No island registered named "island".',
            fn () => $this->root->run('island', $request),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->badValueExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory::class);
        $this->unactivatedRegistry = Mockery::mock(Archipelago_Internal_Registry_UnactivatedIslandRegistry::class);
        $this->activatedRegistry = Mockery::mock(Archipelago_Internal_Registry_ActivatedIslandRegistry::class);
        $this->islandFactory = Mockery::mock(Archipelago_Internal_IslandFactory::class);
        $this->invoker = Mockery::mock(Archipelago_Internal_Invoker::class);

        $this->root = new Archipelago_Internal_Root(
            $this->badValueExceptionFactory,
            $this->unactivatedRegistry,
            $this->activatedRegistry,
            $this->islandFactory,
            $this->invoker,
        );
    }

    private function assertThrowsFactoryException(string $message, callable $action): void
    {
        $exception = Mockery::mock(Archipelago_BadValueException::class);

        $this->badValueExceptionFactory
            ->expects()
            ->make($message)
            ->andReturn($exception);

        try {
            $action();
        } catch (Archipelago_BadValueException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_BadValueException::class . ' to be thrown.');
    }
}
