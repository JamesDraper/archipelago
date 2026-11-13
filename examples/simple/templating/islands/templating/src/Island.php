<?php
declare(strict_types=1);

namespace Examples\Simple\Islands\Templating;

use Archipelago_Island;

class Island implements Archipelago_Island
{
    public function getName()
    {
        return 'simple.templating';
    }

    public function getIngress()
    {
        return new Ingress();
    }

    public function getBootstrapper()
    {
        return null;
    }

    public function getConfigurator()
    {
        return null;
    }

    public function getAdapter()
    {
        return null;
    }
}
