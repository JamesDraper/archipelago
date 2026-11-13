<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

use Archipelago_Island_Inner_Ingress;
use Archipelago_Island_Inner_Request;

use InvalidArgumentException;
use LogicException;

use function sprintf;

class Ingress implements Archipelago_Island_Inner_Ingress
{
    private ?TaxRate $taxRate = null;

    /**
     * Called by the configurator before the first request.
     */
    public function setTaxRate(TaxRate $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function invoke(Archipelago_Island_Inner_Request $request): Response
    {
        if (!$request instanceof Request) {
            throw new InvalidArgumentException(sprintf('Unsupported request: %s', $request::class));
        }

        if (null === $this->taxRate) {
            throw new LogicException('Ingress has not been configured with a tax rate.');
        }

        $net = $request->basket->net();
        $tax = $this->taxRate->of($net);

        return new Response(new Totals($net, $tax, $net + $tax));
    }
}
