<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

use InvalidArgumentException;

use function sprintf;
use function round;

class TaxRate
{
    /**
     * @param int $basisPoints e.g. 2000 for 20%.
     */
    public function __construct(public readonly int $basisPoints)
    {
        if ($basisPoints < 0) {
            throw new InvalidArgumentException(sprintf('Tax rate cannot be negative, got %d.', $basisPoints));
        }
    }

    /**
     * The tax due on a net amount, rounded to the nearest minor unit.
     */
    public function of(int $net): int
    {
        return (int) round($net * $this->basisPoints / 10000);
    }
}
