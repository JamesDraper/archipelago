<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

use InvalidArgumentException;

use function sprintf;

class Item
{
    /**
     * @param int $price In minor units, e.g. pence.
     */
    public function __construct(
        public readonly int $price,
    ) {
        if ($price < 0) {
            throw new InvalidArgumentException(sprintf('Item price cannot be negative, got %d.', $price));
        }
    }
}
