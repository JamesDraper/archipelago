<?php
declare(strict_types=1);

namespace Tests\Unit;

use Archipelago_Internal_Boundary_NullBootstrapper;
use Archipelago_Internal_Boundary_NullConfigurator;
use Archipelago_Internal_Boundary_NullAdapter;
use Archipelago_Internal_Validator_Validator;
use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Boundary_Adapter;
use Archipelago_Internal_IslandFactory;
use Archipelago_Island_Inner_Ingress;
use Archipelago_Internal_Island;
use Archipelago_Island;


use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class IslandFactoryTest extends TestCase
{
    private Archipelago_Internal_Validator_Validator&MockInterface $validator;

    private Archipelago_Internal_IslandFactory $factory;

    #[Test]
    public function it_makes_an_island(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $island = $this->mockIsland('name', $ingress, $bootstrapper, $configurator, $adapter);

        $this
            ->validator
            ->expects()
            ->validate($island, 'name', $ingress, $bootstrapper, $configurator, $adapter);

        $internalIsland = $this->factory->make($island);

        $this->assertInstanceOf(Archipelago_Internal_Island::class, $internalIsland);
        $this->assertSame('name', $internalIsland->getName());
        $this->assertSame($ingress, $internalIsland->getIngress());
        $this->assertSame($bootstrapper, $internalIsland->getBootstrapper());
        $this->assertSame($configurator, $internalIsland->getConfigurator());
        $this->assertSame($adapter, $internalIsland->getAdapter());
    }

    #[Test]
    public function it_uses_a_null_bootstrapper_when_the_island_has_none(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $island = $this->mockIsland('name', $ingress, null, $configurator, $adapter);

        $this
            ->validator
            ->expects()
            ->validate($island, 'name', $ingress, null, $configurator, $adapter);

        $internalIsland = $this->factory->make($island);

        $this->assertInstanceOf(Archipelago_Internal_Boundary_NullBootstrapper::class, $internalIsland->getBootstrapper());
    }

    #[Test]
    public function it_uses_a_null_configurator_when_the_island_has_none(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $adapter = Mockery::mock(Archipelago_Island_Boundary_Adapter::class);

        $island = $this->mockIsland('name', $ingress, $bootstrapper, null, $adapter);

        $this
            ->validator
            ->expects()
            ->validate($island, 'name', $ingress, $bootstrapper, null, $adapter);

        $internalIsland = $this->factory->make($island);

        $this->assertInstanceOf(Archipelago_Internal_Boundary_NullConfigurator::class, $internalIsland->getConfigurator());
    }

    #[Test]
    public function it_uses_a_null_adapter_when_the_island_has_none(): void
    {
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);

        $island = $this->mockIsland('name', $ingress, $bootstrapper, $configurator, null);

        $this
            ->validator
            ->expects()
            ->validate($island, 'name', $ingress, $bootstrapper, $configurator, null);

        $internalIsland = $this->factory->make($island);

        $this->assertInstanceOf(Archipelago_Internal_Boundary_NullAdapter::class, $internalIsland->getAdapter());
    }

    #[Test]
    public function it_reuses_the_null_boundary_classes(): void
    {
        $firstIngress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $secondIngress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);

        $firstIsland = $this->mockIsland('first', $firstIngress, null, null, null);
        $secondIsland = $this->mockIsland('second', $secondIngress, null, null, null);

        $this
            ->validator
            ->expects()
            ->validate($firstIsland, 'first', $firstIngress, null, null, null);

        $this
            ->validator
            ->expects()
            ->validate($secondIsland, 'second', $secondIngress, null, null, null);

        $firstInternalIsland = $this->factory->make($firstIsland);
        $secondInternalIsland = $this->factory->make($secondIsland);

        $this->assertSame($firstInternalIsland->getBootstrapper(), $secondInternalIsland->getBootstrapper());
        $this->assertSame($firstInternalIsland->getConfigurator(), $secondInternalIsland->getConfigurator());
        $this->assertSame($firstInternalIsland->getAdapter(), $secondInternalIsland->getAdapter());
    }

    private function mockIsland(
        string $name,
        Archipelago_Island_Inner_Ingress $ingress,
        ?Archipelago_Island_Boundary_Bootstrapper $bootstrapper,
        ?Archipelago_Island_Boundary_Configurator $configurator,
        ?Archipelago_Island_Boundary_Adapter $adapter,
    ): Archipelago_Island&MockInterface {
        $island = Mockery::mock(Archipelago_Island::class);

        $island
            ->expects()
            ->getName()
            ->andReturn($name);

        $island
            ->expects()
            ->getIngress()
            ->andReturn($ingress);
        
        $island
            ->expects()
            ->getBootstrapper()
            ->andReturn($bootstrapper);
        
        $island
            ->expects()
            ->getConfigurator()
            ->andReturn($configurator);
        
        $island
            ->expects()
            ->getAdapter()
            ->andReturn($adapter);

        return $island;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = Mockery::mock(Archipelago_Internal_Validator_Validator::class);
        $this->factory = new Archipelago_Internal_IslandFactory($this->validator);
    }
}
