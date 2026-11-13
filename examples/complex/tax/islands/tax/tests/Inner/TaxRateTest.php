<?php
declare(strict_types=1);

namespace Examples\Complex\Tax\Tests\Inner;

use Examples\Complex\Islands\Tax\Inner\TaxRate;

use InvalidArgumentException;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaxRateTest extends TestCase
{
    #[Test]
    public function it_calculates_the_tax_on_a_net_amount(): void
    {
        $this->assertSame(200, (new TaxRate(2000))->of(1000));
    }

    #[Test]
    public function it_rounds_to_the_nearest_minor_unit(): void
    {
        // 999 * 20% = 199.8, which rounds to 200.
        $this->assertSame(200, (new TaxRate(2000))->of(999));
    }

    #[Test]
    public function a_zero_rate_charges_no_tax(): void
    {
        $this->assertSame(0, (new TaxRate(0))->of(1000));
    }

    #[Test]
    public function it_rejects_a_negative_rate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TaxRate(-1);
    }
}
