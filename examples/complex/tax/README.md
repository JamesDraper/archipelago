# Complex Tax Island

Imagine you had a shopping basket object in your legacy system. This basket object had a single public property: an array named $items. Each item in this array was not an object, but an associative array of items such as SKU etc. Obviously that's not ideal, and even if we could pass the data directly into an island like that, we shouldn't.

This example moves that calculation into an island, and uses every part of the island to do it.

## What it shows

- **The application keeps its own objects.** The application has a `Basket` whose public `$items` array holds an associative array per item: a SKU, a name, a price, whatever the shop stores. The application puts its `Basket` into the outer request, and gets its own `Totals` object back in the outer response. It never sees anything from inside the island.
- **The adapter converts at the boundary.** `Boundary\Adapter` turns the application's basket into an inner `Basket` of inner `Item` objects. The island only cares about prices, so the SKU and other product data are left behind. On the way back, the adapter turns the inner `Totals` into the application's `Totals`.
- **The configurator injects an inner object.** `Boundary\Configurator` builds an `Inner\TaxRate` of 20% and injects it into the ingress before the first request is handled.
- **The bootstrapper keeps loading lazy.** On register, it eager loads only the outer request, because the application needs that class to make its first call. On first run, it loads `autoload.php`, a small PSR-4 autoloader for the rest of the island, so none of the inner code is loaded until the island is actually used.

## Tests

Only the inner island is tested. The boundary classes are thin translation and wiring code, and the inner code is where the logic lives.

The ingress takes its `TaxRate` through `setTaxRate()`, which is the configurator's injection point. That doubles as a seam for tests: `IngressTest` injects a Mockery mock of `TaxRate`, so the ingress is tested on its own, and `TaxRateTest` covers the rate arithmetic separately.

The island has its own `composer.json`, so it can be installed and tested on its own. Archipelago is pulled in from the root of this repository through a path repository.
