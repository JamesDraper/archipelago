<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Outer;

use Examples\Complex\App\Basket;

use Archipelago_Island_Outer_Request;

class Request implements Archipelago_Island_Outer_Request
{
    public function __construct(
        public readonly Basket $basket,
    ) {}
}
