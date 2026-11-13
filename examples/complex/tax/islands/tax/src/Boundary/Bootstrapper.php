<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Boundary;

use Archipelago_Island_Boundary_Bootstrapper;

use function dirname;

class Bootstrapper implements Archipelago_Island_Boundary_Bootstrapper
{
    /**
     * The application needs the outer request to make its first call, which is
     * before the autoloader below has been registered.
     */
    public function onRegister(): void
    {
        require_once dirname(__DIR__) . '/Outer/Request.php';
    }

    /**
     * Everything else is loaded on demand, once the island is actually used.
     */
    public function onFirstRun(): void
    {
        require_once dirname(__DIR__, 2) . '/autoload.php';
    }
}
