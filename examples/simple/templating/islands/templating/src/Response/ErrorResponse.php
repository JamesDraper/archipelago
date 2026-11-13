<?php
declare(strict_types=1);

namespace Examples\Simple\Islands\Templating\Response;

use Archipelago_Island_Inner_Response;
use Archipelago_Island_Outer_Response;

class ErrorResponse implements Archipelago_Island_Inner_Response, Archipelago_Island_Outer_Response
{
    public function __construct(
        public readonly string $message,
    ) {}
}
