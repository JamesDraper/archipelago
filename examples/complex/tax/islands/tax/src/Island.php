<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax;

use Examples\Complex\Islands\Tax\Boundary\Bootstrapper;
use Examples\Complex\Islands\Tax\Boundary\Configurator;
use Examples\Complex\Islands\Tax\Boundary\Adapter;
use Examples\Complex\Islands\Tax\Inner\Ingress;

use Archipelago_Island;

/**
 * Archipelago asks for these objects when the island is registered, before the
 * bootstrapper's autoloader exists, so each getter loads its own class.
 */
class Island implements Archipelago_Island
{
    public function getName(): string
    {
        return 'complex.tax';
    }

    public function getIngress(): Ingress
    {
        require_once __DIR__ . '/Inner/Ingress.php';

        return new Ingress();
    }

    public function getBootstrapper(): Bootstrapper
    {
        require_once __DIR__ . '/Boundary/Bootstrapper.php';

        return new Bootstrapper();
    }

    public function getConfigurator(): Configurator
    {
        require_once __DIR__ . '/Boundary/Configurator.php';

        return new Configurator();
    }

    public function getAdapter(): Adapter
    {
        require_once __DIR__ . '/Boundary/Adapter.php';

        return new Adapter();
    }
}
