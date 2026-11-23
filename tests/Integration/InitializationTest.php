<?php
declare(strict_types=1);

namespace Tests\Integration;

use Archipelago;

use PHPUnit\Framework\Attributes\Test;

/**
 * Tests that assert that an island initialises properly, calling bootstrapper
 * and configurator methods in the right order.
 */
final class InitializationTest extends TestCase
{
    #[Test]
    public function it_calls_onRegister(): void
    {
        // Assert that onRegister() is called when an island is registered
        // if the island has a bootstrapper.

        $ingress = $this->mockIngress();
        $bootstrapper = $this->mockBootstrapper();
        $island = $this->mockIsland('island', $ingress, $bootstrapper);

        $bootstrapper
            ->expects()
            ->onRegister();

        Archipelago::register($island);
    }

    #[Test]
    public function it_does_not_call_onRegister(): void
    {
        // Assert that onRegister() is not called when an island is registered
        // if the island has no bootstrapper.

        $ingress = $this->mockIngress();
        $island = $this->mockIsland('island', $ingress);

        Archipelago::register($island);
    }

    #[Test]
    public function it_calls_onFirstRun(): void
    {
        // Assert that onFirstRun() is called when an island is run
        // if the island has a bootstrapper.

        $ingress = $this->mockIngress();
        $bootstrapper = $this->mockBootstrapper();
        $island = $this->mockIsland('island', $ingress, $bootstrapper);
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

        $ingress
            ->expects()
            ->invoke($request)
            ->andReturn($response)
            ->globally()
            ->ordered();

        Archipelago::run('island', $request);
    }

    #[Test]
    public function it_calls_configure(): void
    {
        // Assert that configure() is not called when an island is run
        // if the island has a configurator.

        $ingress = $this->mockIngress();
        $configurator = $this->mockConfigurator();
        $island = $this->mockIsland('island', $ingress, configurator: $configurator);
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        Archipelago::register($island);

        $configurator
            ->expects()
            ->configure($ingress)
            ->globally()
            ->ordered();

        $ingress
            ->expects()
            ->invoke($request)
            ->andReturn($response)
            ->globally()
            ->ordered();

        Archipelago::run('island', $request);
    }

    #[Test]
    public function it_calls_onRegister_onFirstRun_and_configure(): void
    {
        // Assert that onRegister(), onFirstRun(), and configure() 
        // are all called in the right order before the ingress is invoked
        // if the island has a bootstrapper and a configurator.

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
            ->andReturn($response)
            ->globally()
            ->ordered();

        Archipelago::run('island', $request);        
    }

    #[Test]
    public function it_does_not_call_onRegister_onFirstRun_or_configure(): void
    {
        // Assert that onRegister(), onFirstRun(), and configure() are not
        // called when an island is run if the island has no bootstrapper
        // and no configurator.

        $ingress = $this->mockIngress();
        $island = $this->mockIsland('island', $ingress);
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        Archipelago::register($island);

        $ingress
            ->expects()
            ->invoke($request)
            ->andReturn($response);

        Archipelago::run('island', $request);
    }
}
