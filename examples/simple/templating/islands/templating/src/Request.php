<?php
declare(strict_types=1);

namespace Examples\Simple\Islands\Templating;

use Archipelago_Island_Inner_Request;
use Archipelago_Island_Outer_Request;

class Request implements Archipelago_Island_Inner_Request, Archipelago_Island_Outer_Request
{
    /**
     * @param array<string, mixed> $vars
     */
    public function __construct(
        public readonly string $template,
        public readonly array $vars = [],
    ) {}
}
