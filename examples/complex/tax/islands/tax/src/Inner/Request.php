<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

use Archipelago_Island_Inner_Request;

class Request implements Archipelago_Island_Inner_Request
{
    public function __construct(
        public readonly Basket $basket,
    ) {}
}
