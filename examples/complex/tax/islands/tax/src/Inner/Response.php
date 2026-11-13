<?php
declare(strict_types=1);

namespace Examples\Complex\Islands\Tax\Inner;

use Archipelago_Island_Inner_Response;

class Response implements Archipelago_Island_Inner_Response
{
    public function __construct(
        public readonly Totals $totals,
    ) {}
}
