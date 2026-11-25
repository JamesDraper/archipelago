<?php

declare(strict_types=1);

namespace Tests\Unit\Registry;

use Archipelago_Internal_Registry_UnactivatedIslandRegistry;
use Archipelago_Island_Boundary_Bootstrapper;
use Archipelago_Internal_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

final class UnactivatedIslandRegistryTest extends TestCase
{
    private Archipelago_Internal_Registry_UnactivatedIslandRegistry $registry;

    #[Test]
    public function it_runs_the_register_hook_when_an_island_is_added(): void
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

    #[Test]
    public function it_removes_an_island(): void
    {
        $this->registry->add($this->mockIsland());

        $this->registry->remove('island');

        $this->assertNull($this->registry->get('island'));
    }

    #[Test]
    public function it_ignores_removing_an_island_that_was_not_added(): void
    {
        $island = $this->mockIsland();

        $this->registry->add($island);

        $this->registry->remove('other.island');

        $this->assertSame($island, $this->registry->get('island'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new Archipelago_Internal_Registry_UnactivatedIslandRegistry();
    }

    private function mockIsland(): Archipelago_Internal_Island&MockInterface
    {
        $bootstrapper = Mockery::mock(Archipelago_Island_Boundary_Bootstrapper::class);
        $island = Mockery::mock(Archipelago_Internal_Island::class);

        $island
            ->expects()
            ->getName()
            ->atLeast()
            ->once()
            ->andReturn('island');

        $island
            ->expects()
            ->getBootstrapper()
            ->andReturn($bootstrapper);

        $bootstrapper
            ->expects()
            ->onRegister();

        return $island;
    }
}
