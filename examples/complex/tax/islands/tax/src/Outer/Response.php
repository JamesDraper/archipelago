<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Outer;

use Examples\Complex\App\Totals;

use Archipelago_Island_Outer_Response;

class Response implements Archipelago_Island_Outer_Response
{
    public function __construct(
        public readonly Totals $totals,
    ) {}
}
