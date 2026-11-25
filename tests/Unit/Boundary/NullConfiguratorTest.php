<?php
declare(strict_types=1);

namespace Tests\Unit\Boundary;

use Archipelago_Internal_Boundary_NullConfigurator;
use Archipelago_Island_Boundary_Configurator;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class NullConfiguratorTest extends TestCase
{
    #[Test]
    public function it_is_a_configurator(): void
    {
        $configurator = new Archipelago_Internal_Boundary_NullConfigurator();

        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertInstanceOf(Archipelago_Island_Boundary_Configurator::class, $configurator);
    }
}
