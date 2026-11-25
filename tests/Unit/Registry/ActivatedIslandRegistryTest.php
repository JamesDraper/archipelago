<?php

declare(strict_types=1);

namespace Tests\Unit\Registry;

use Archipelago_Internal_Registry_ActivatedIslandRegistry;
use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Inner_Ingress;
use Archipelago_Internal_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class ActivatedIslandRegistryTest extends TestCase
{
    private Archipelago_Internal_Registry_ActivatedIslandRegistry $registry;

    #[Test]
    public function it_runs_the_first_run_hook_and_configures_the_ingress_when_an_island_is_added(): void
    {
        $island = $this->mockIsland();

        $this->registry->add($island);
    }

    #[Test]
    public function it_gets_an_island_that_was_added_by_its_name(): void
    {
        $island = $this->mockIsland();

        $this->registry->add($island);

        $this->assertSame($island, $this->registry->get('island'));
    }

    #[Test]
    public function it_returns_null_for_an_island_that_was_not_added(): void
    {
        $this->registry->add($this->mockIsland());

        $this->assertNull($this->registry->get('other.island'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new Archipelago_Internal_Registry_ActivatedIslandRegistry();
    }

    private function mockIsland(): Archipelago_Internal_Island&MockInterface
    {
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $configurator = Mockery::mock(Archipelago_Island_Boundary_Configurator::class);
        $ingress = Mockery::mock(Archipelago_Island_Inner_Ingress::class);
        $island = Mockery::mock(Archipelago_Internal_Island::class);

        $island
            ->expects()
            ->getName()
            ->atLeast()
            ->once()
            ->andReturn('island');

        $island
            ->allows()
            ->getBootstrapper()
            ->andReturn($bootstrapper);

        $island
            ->allows()
            ->getConfigurator()
            ->andReturn($configurator);

        $island
            ->allows()
            ->getIngress()
            ->andReturn($ingress);

        $bootstrapper
            ->allows()
            ->onFirstRun();

        $configurator
            ->allows()
            ->configure($ingress);

        return $island;
    }
}
