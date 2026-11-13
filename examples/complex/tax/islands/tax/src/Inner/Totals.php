<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

class Totals
{
    public function __construct(
        public readonly int $net,
        public readonly int $tax,
        public readonly int $gross,
    ) {}
}
