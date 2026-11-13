<?php
declare(strict_types=1);

namespace Examples\Complex\App;

use Examples\Complex\Islands\Tax\Outer\Response;
use Examples\Complex\Islands\Tax\Outer\Request;
use Examples\Complex\Islands\Tax\Island;

use LogicException;
use Archipelago;

use function sprintf;

require_once __DIR__ . '/islands/tax/src/Island.php';

/**
 * The application's basket. Each item is an associative array of product data,
 * of which the island only cares about the price (in minor units, e.g. pence).
 */
class Basket
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $items = [];
}

/**
 * The application's own totals object. The island never sees this class.
 */
class Totals
{
    public function __construct(
        public readonly int $net,
        public readonly int $tax,
        public readonly int $gross,
    ) {}
}

/**
 * @throws LogicException
 */
function basketTotals(Basket $basket): Totals
{
    $response = Archipelago::run('complex.tax', new Request($basket));

    if (!$response instanceof Response) {
        throw new LogicException(sprintf('Unexpected response type: %s', $response::class));
    }

    return $response->totals;
}

Archipelago::register(new Island());
