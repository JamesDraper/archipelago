<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Boundary;

use Examples\Complex\Islands\Tax\Inner\TaxRate;
use Examples\Complex\Islands\Tax\Inner\Ingress;

use Archipelago_Island_Boundary_Configurator;
use Archipelago_Island_Inner_Ingress;

use InvalidArgumentException;

use function sprintf;

class Configurator implements Archipelago_Island_Boundary_Configurator
{
    /**
     * In basis points: 2000 is 20%.
     */
    private const TAX_RATE = 2000;

    public function configure(Archipelago_Island_Inner_Ingress $ingress): void
    {
        if (!$ingress instanceof Ingress) {
            throw new InvalidArgumentException(sprintf('Unsupported ingress: %s', $ingress::class));
        }

        $ingress->setTaxRate(new TaxRate(self::TAX_RATE));
    }
}
