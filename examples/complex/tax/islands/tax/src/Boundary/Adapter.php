<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Boundary;

use Examples\Complex\Islands\Tax\Inner\Response as InnerResponse;
use Examples\Complex\Islands\Tax\Outer\Response as OuterResponse;
use Examples\Complex\Islands\Tax\Inner\Request as InnerRequest;
use Examples\Complex\Islands\Tax\Outer\Request as OuterRequest;
use Examples\Complex\Islands\Tax\Inner\Basket as InnerBasket;
use Examples\Complex\Islands\Tax\Inner\Totals as InnerTotals;
use Examples\Complex\App\Basket as AppBasket;
use Examples\Complex\App\Totals as AppTotals;
use Examples\Complex\Islands\Tax\Inner\Item;

use Archipelago_Island_Boundary_Adapter;
use Archipelago_Island_Inner_Response;
use Archipelago_Island_Outer_Request;

use InvalidArgumentException;

use function is_array;
use function sprintf;
use function is_int;

/**
 * The only place that knows about both the application's objects and the
 * island's objects.
 */
class Adapter implements Archipelago_Island_Boundary_Adapter
{
    public function requestIn(Archipelago_Island_Outer_Request $request): InnerRequest
    {
        if (!$request instanceof OuterRequest) {
            throw new InvalidArgumentException(sprintf('Unsupported request: %s', $request::class));
        }

        return new InnerRequest($this->basketIn($request->basket));
    }

    public function responseOut(Archipelago_Island_Inner_Response $response): OuterResponse
    {
        if (!$response instanceof InnerResponse) {
            throw new InvalidArgumentException(sprintf('Unsupported response: %s', $response::class));
        }

        return new OuterResponse($this->totalsOut($response->totals));
    }

    private function basketIn(AppBasket $basket): InnerBasket
    {
        $items = [];

        foreach ($basket->items as $index => $item) {
            // The SKU and any other product data stay behind; the island only
            // needs the price.
            if (!is_array($item) || !isset($item['price']) || !is_int($item['price'])) {
                throw new InvalidArgumentException(sprintf('Basket item %d has no integer price.', $index));
            }

            $items[] = new Item($item['price']);
        }

        return new InnerBasket($items);
    }

    private function totalsOut(InnerTotals $totals): AppTotals
    {
        return new AppTotals($totals->net, $totals->tax, $totals->gross);
    }
}
