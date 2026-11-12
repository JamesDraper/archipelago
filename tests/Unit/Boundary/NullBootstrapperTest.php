<?php
declare(strict_types=1);

namespace Tests\Unit\Boundary;

use Archipelago_Internal_Boundary_NullBootstrapper;
use Archipelago_Island_Boundary_Bootstrapper;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class NullBootstrapperTest extends TestCase
{
    #[Test]
    public function it_is_a_bootstrapper(): void
    {
        $bootstrapper = new Archipelago_Internal_Boundary_NullBootstrapper();

        $this->assertInstanceOf(Archipelago_Island_Boundary_Bootstrapper::class, $bootstrapper);
    }
}
