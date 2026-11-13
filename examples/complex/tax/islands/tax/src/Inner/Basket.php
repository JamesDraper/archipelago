<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

class Basket
{
    /**
     * @param list<Item> $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public function net(): int
    {
        $net = 0;

        foreach ($this->items as $item) {
            $net += $item->price;
        }

        return $net;
    }
}
